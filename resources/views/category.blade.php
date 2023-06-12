@extends('layouts.main')

@section('title', 'Kategori Produk')

@section('content')
<div class="mb-4 d-flex justify-content-end">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Tambah Kategori</button>
</div>

<table id="table-category" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nama Kategori</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Konsumsi</td>
      <td>
        <button class="btn btn-warning btn-sm me-1">
            Edit
        </button>
        <button class="btn btn-danger btn-sm">
            Hapus
        </button>
      </td>
    </tr>
    <tr>
      <td>Pembersih</td>
      <td>
        <button class="btn btn-warning btn-sm me-1">
            Edit
        </button>
        <button class="btn btn-danger btn-sm">
            Hapus
        </button>
      </td>
    </tr>
    <tr>
      <td>Alat Kantor</td>
      <td>
        <button class="btn btn-warning btn-sm me-1">
            Edit
        </button>
        <button class="btn btn-danger btn-sm">
            Hapus
        </button>
      </td>
    </tr>
  </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-add-category">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input name="category_name" type="text" class="form-control" placeholder="Masukkan nama kategori" required />
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
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-edit-category">
        <input type="hidden" name="id" />
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input name="category_name" type="text" class="form-control" placeholder="Masukkan nama kategori" required />
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
        table = $('#table-category').DataTable({
            processing: true,
            ajax: {
                url: baseUrl+'/api/product-category',
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
                    name: 'category_name',
                    data: 'category_name',
                },
                {
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, _, row) => {
                        return `
                            <button onclick="openEditCategoryModal(${row.id})" class="btn btn-warning btn-sm me-1">
                                Edit
                            </button>
                            <button onclick="deleteCategory(${row.id})" class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        `;
                    }
                },
            ]
        });

        $('#form-add-category').on('submit', async function(e) {
            e.preventDefault();
            const formData = getFormObj('form-add-category');
            $.ajax({
                url: baseUrl+'/api/product-category',
                type: 'post',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                data: formData,
                success: (res) => {
                    if (res.id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil menambahkan kategori',
                        });
                        addCategoryModal.hide();
                        resetAddCategoryForm();
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

        $('#form-edit-category').on('submit', async function(e) {
            e.preventDefault();
            const formData = getFormObj('form-edit-category');
            $.ajax({
                url: baseUrl+'/api/product-category/'+formData.id,
                type: 'put',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                data: formData,
                success: (res) => {
                    if (res.id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil mengedit kategori',
                        });
                        editCategoryModal.hide();
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

    const addCategoryModal = new bootstrap.Modal('#addCategoryModal');

    function resetAddCategoryForm() {
        $('#form-add-category input[name="category_name"]').val('');
    }

    const editCategoryModal = new bootstrap.Modal('#editCategoryModal');

    async function openEditCategoryModal(categoryId) {
        const category = await $.ajax({
            url: baseUrl+'/api/product-category/'+categoryId,
            type: 'get',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            }
        });

        $('#form-edit-category input[name="id"]').val(category.id);
        $('#form-edit-category input[name="category_name"]').val(category.category_name);

        editCategoryModal.show();
    }

    async function deleteCategory(categoryId) {
        const confirmRes = await Swal.fire({
            title: 'Anda yakin ingin menghapus kategori ini?',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
        });

        if(!confirmRes.isConfirmed) return;

        $.ajax({
            url: baseUrl+'/api/product-category/'+categoryId,
            type: 'delete',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            },
            success: (res) => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil manghapus kategori',
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
