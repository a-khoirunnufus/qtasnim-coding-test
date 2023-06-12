@extends('layouts.main')

@section('title', 'Produk')

@section('content')
<div class="mb-4 d-flex justify-content-end">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Produk</button>
</div>

<table id="table-product" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nama</th>
      <th>Kategori</th>
      <th>Stok Awal</th>
      <th>Terjual</th>
      <th>Stok Tersedia</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Tambah Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-add-product">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input name="product_name" type="text" class="form-control" placeholder="Masukkan nama produk" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" required>
                    <option disabled selected>Pilih Kategori</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Stok Awal</label>
                <input name="quantity" type="number" class="form-control" placeholder="Masukkan jumlah stok awal" required />
            </div>
        </div>
        <div class="modal-footer">
            <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
            <button type="submit" class="btn btn-success">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProductModalLabel">Edit Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-edit-product">
        <input type="hidden" name="id" />
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input name="product_name" type="text" class="form-control" placeholder="Masukkan nama produk" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" required>
                    <option disabled selected>Pilih Kategori</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
            <button type="submit" class="btn btn-warning">Edit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(async function () {
        table = $('#table-product').DataTable({
            processing: true,
            ajax: {
                url: baseUrl+'/api/product',
                type: 'get',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                dataSrc: "",
            },
            stateSave: false,
            order: [[0, 'asc']],
            columns: [
                {
                    name: 'product_name',
                    data: 'product_name',
                },
                {
                    name: 'category_name',
                    data: 'category_name',
                },
                {
                    name: 'quantity',
                    data: 'quantity',
                    searchable: false,
                },
                {
                    name: 'sold',
                    data: 'sold',
                    searchable: false,
                },
                {
                    name: 'available',
                    data: 'available',
                    searchable: false,
                },
                {
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, _, row) => {
                        return `
                            <button onclick="openEditProductModal(${row.id})" class="btn btn-warning btn-sm me-1">
                                Edit
                            </button>
                            <button onclick="deleteProduct(${row.id})" class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        `;
                    }
                },
            ]
        });

        // initilize select category option
        $.ajax({
            url: baseUrl+'/api/product-category?sort_by=category_name&order_by=asc',
            type: 'get',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            },
            success: (data) => {
                data.forEach(category => {
                    $('#form-add-product select[name="category_id"]').append(`
                        <option value="${category.id}">${category.category_name}</option>
                    `);
                    $('#form-edit-product select[name="category_id"]').append(`
                        <option value="${category.id}">${category.category_name}</option>
                    `);
                });
            }
        });

        $('#form-add-product').on('submit', async function(e) {
            e.preventDefault();
            const formData = getFormObj('form-add-product');
            $.ajax({
                url: baseUrl+'/api/product',
                type: 'post',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                data: formData,
                success: (res) => {
                    if (res.id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil menambahkan produk',
                        });
                        addProductModal.hide();
                        resetAddProductForm();
                        table.ajax.reload();
                    }
                },
                error: (jqxhr) => {
                    const res = jqxhr.responseJSON;
                    if (res.errors) {
                        formValidationFailHandler(jqxhr);
                    }
                    if (res.error) {
                        Swal.fire({
                            icon: 'error',
                            title: res.error,
                        });
                    }
                }
            });
        });

        $('#form-edit-product').on('submit', async function(e) {
            e.preventDefault();
            const formData = getFormObj('form-edit-product');
            $.ajax({
                url: baseUrl+'/api/product/'+formData.id,
                type: 'put',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                data: formData,
                success: (res) => {
                    if (res.id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil mengedit produk',
                        });
                        editProductModal.hide();
                        table.ajax.reload();
                    }
                },
                error: (jqxhr) => {
                    const res = jqxhr.responseJSON;
                    if (res.errors) {
                        formValidationFailHandler(jqxhr);
                    }
                    if (res.error) {
                        Swal.fire({
                            icon: 'error',
                            title: res.error,
                        });
                    }
                }
            });
        });
    });

    let table = null;

    const addProductModal = new bootstrap.Modal('#addProductModal');

    function resetAddProductForm() {
        $('#form-add-product input[name="product_name"]').val('');
        $('#form-add-product select[name="category_id"]').val('').change();
        $('#form-add-product input[name="quantity"]').val('');
    }

    const editProductModal = new bootstrap.Modal('#editProductModal');

    async function openEditProductModal(productId) {
        const product = await $.ajax({
            url: baseUrl+'/api/product/'+productId,
            type: 'get',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            }
        });

        $('#form-edit-product input[name="id"]').val(product.id);
        $('#form-edit-product input[name="product_name"]').val(product.product_name);
        $('#form-edit-product select[name="category_id"]').val(product.category_id);

        editProductModal.show();
    }

    async function deleteProduct(productId) {
        const confirmRes = await Swal.fire({
            title: 'Anda yakin ingin menghapus produk ini?',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
        });

        if(!confirmRes.isConfirmed) return;

        $.ajax({
            url: baseUrl+'/api/product/'+productId,
            type: 'delete',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            },
            success: (res) => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil manghapus produk',
                    });
                    table.ajax.reload();
                }
            },
            error: (jqxhr) => {
                const res = jqxhr.responseJSON;
                if (res.errors) {
                    formValidationFailHandler(jqxhr);
                }
                if (res.error) {
                    Swal.fire({
                        icon: 'error',
                        title: res.error,
                    });
                }
            }
        });
    }
</script>
@endsection
