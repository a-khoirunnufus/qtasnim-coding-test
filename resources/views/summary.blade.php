@extends('layouts.main')

@section('title', 'Rangkuman Penjualan')

@section('content')
<div class="mb-4">
    <form id="form-filter" class="d-flex flex-row align-items-end" style="gap: 1rem">
        <div style="width: 200px">
            <label class="form-label">Tanggal Awal</label>
            <input name="from_date" type="date" class="form-control" required />
        </div>
        <div style="width: 200px">
            <label class="form-label">Tanggal Akhir</label>
            <input name="to_date" type="date" class="form-control" required />
        </div>
        <div style="width: 200px">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
</div>

<table id="table-summary" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nama Kategori</th>
      <th>Jumlah Penjualan</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Konsumsi</td>
      <td>75</td>
    </tr>
    <tr>
      <td>Pembersih</td>
      <td>45</td>
    </tr>
  </tbody>
</table>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        initTable();

        async function initTable() {
            $('#table-summary').DataTable({
                destroy: true,
                processing: true,
                ajax: {
                    url: baseUrl+'/api/sales-summary'+getParams(),
                    type: 'get',
                    headers: {
                        'Authorization': 'Basic ' + await getAuthCode(),
                    },
                    dataSrc: "sales_data",
                },
                stateSave: false,
                order: [[1, 'desc']],
                columns: [
                    {
                        name: 'category_name',
                        data: 'category_name',
                    },
                    {
                        name: 'sales',
                        data: 'sales',
                    },
                ]
            });
        }

        function getParams() {
            const fromDate = $('#form-filter input[name="from_date"]').val();
            const toDate = $('#form-filter input[name="to_date"]').val();
            if (fromDate && toDate) {
                return `?from_date=${fromDate}&to_date=${toDate}`;
            }
            return '';
        }

        $('#form-filter').on('submit', function(e) {
            e.preventDefault();
            initTable();
        })
    });
</script>
@endsection
