@extends('layouts.main')

@section('title', 'Transaksi')

@section('content')
<div class="mb-4 d-flex justify-content-end">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Tambah Transaksi</button>
</div>

<table id="table-transaction" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Id Transaksi</th>
            <th>Nama Produk</th>
            <th>Nama Kategori</th>
            <th>Jumlah Terjual</th>
            <th>Tanggal Transaksi</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-add-transaction">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Produk</label>
                <select name="product_id" class="form-select" required>
                    <option disabled selected>Pilih Produk</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Terjual</label>
                <input name="quantity" type="number" class="form-control" placeholder="Masukkan jumlah terjual" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                <input name="transaction_date" type="date" class="form-control" placeholder="Masukkan tanggal transaksi" required />
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
@endsection

@section('scripts')
<script>
    $(document).ready(async function () {
        table = $('#table-transaction').DataTable({
            processing: true,
            ajax: {
                url: baseUrl+'/api/transaction',
                type: 'get',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                dataSrc: "",
            },
            stateSave: false,
            order: [[4, 'desc']],
            columns: [
                {
                    name: 'id',
                    data: 'id',
                    searchable: false,
                },
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
                    name: 'transaction_date',
                    data: 'transaction_date',
                },
            ]
        });

        // initilize select product option
        $.ajax({
            url: baseUrl+'/api/product?available_only=yes&sort_by=product_name&order_by=asc',
            type: 'get',
            headers: {
                'Authorization': 'Basic ' + await getAuthCode(),
            },
            success: (data) => {
                data.forEach(product => {
                    $('#form-add-transaction select[name="product_id"]').append(`
                        <option value="${product.id}">${product.product_name}(${product.available})</option>
                    `);
                });
            }
        });

        $('#form-add-transaction').on('submit', async function(e) {
            e.preventDefault();
            const formData = getFormObj('form-add-transaction');
            $.ajax({
                url: baseUrl+'/api/transaction',
                type: 'post',
                headers: {
                    'Authorization': 'Basic ' + await getAuthCode(),
                },
                data: formData,
                success: (res) => {
                    if (res.id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil menambahkan transaksi',
                        });
                        addTransactionModal.hide();
                        resetAddTransactionForm();
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

    const addTransactionModal = new bootstrap.Modal('#addTransactionModal');

    function resetAddTransactionForm() {
        $('#form-add-transaction select[name="product_id"]').val('').change();
        $('#form-add-transaction input[name="quantity"]').val('');
        $('#form-add-transaction input[name="transaction_date"]').val('');
    }
</script>
@endsection
