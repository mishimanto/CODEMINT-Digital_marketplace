@extends('admin.master_layout')
@section('title')
<title>Sales & Charges Report</title>
@endsection

@section('admin-content')
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Sales & Charges Report</h1>
      <!-- <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Sales & Charges Report</div>
      </div> -->
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <form id="reportForm" class="row g-3 align-items-center" method="GET" action="{{ route('admin.reports.sales') }}">
                <div class="col-md-2">
                  <label class="form-label">Report Type</label>
                  <select name="type" class="form-control" id="reportType">
                    <option value="daily" {{ $type==='daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $type==='monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $type==='yearly' ? 'selected' : '' }}>Yearly</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">From Date</label>
                  <input type="date" name="from" value="{{ $from }}" class="form-control" id="fromDate" />
                </div>
                <div class="col-md-3">
                  <label class="form-label">To Date</label>
                  <input type="date" name="to" value="{{ $to }}" class="form-control" id="toDate" />
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <button class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Apply Filter</button>
                  <a class="btn btn-success mr-2" href="{{ route('admin.reports.sales.export', request()->all()) }}"><i class="fas fa-file-export"></i> Export CSV</a>
                  <button type="button" class="btn btn-outline-secondary" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      {{-- KPI Cards --}}
      <div id="printArea">
        <div class="row mt-4">
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total Orders</h4>
                </div>
                <div class="card-body">
                  {{ $total_orders }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-success">
                <i class="fas fa-dollar-sign"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total Earnings</h4>
                </div>
                <div class="card-body">
                  ${{ number_format($total_earning,2) }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-info">
                <i class="fas fa-wallet"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Total Withdraw Amount</h4>
                </div>
                <div class="card-body">
                  ${{ number_format($total_withdraw_amount,2) }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-warning">
                <i class="fas fa-coins"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>Profit (Charge)</h4>
                </div>
                <div class="card-body">
                  ${{ number_format($withdraw_charge,2) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Date Range Info --}}
        <div class="row mt-4 d-print-none">
          <div class="col-12">
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> Showing report from <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>
            </div>
          </div>
        </div>

        {{-- Withdraw Breakdown --}}
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4>Withdraw Charges Breakdown</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th>Withdraw ID</th>
                        <th>Provider</th>
                        <th>Withdraw Date</th>
                        <th class="text-right">Withdraw Amount</th>
                        <th class="text-right">Profit (Charge)</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($withdraws as $w)
                      <tr>
                        <td>#{{ $w->id }}</td>
                        <td>{{ $w->provider->name ?? 'N/A' }}</td>
                        <td>{{ $w->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-right">${{ number_format($w->total_amount,2) }}</td>
                        <td class="text-right">${{ number_format($w->calculated_charge,2) }}</td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">No withdraws found in the selected date range</td>
                      </tr>
                      @endforelse
                      @if($withdraws->count() > 0)
                      <tr class="bg-light">
                        <td colspan="3" class="text-right"><strong>Totals:</strong></td>
                        <td class="text-right"><strong>${{ number_format($withdraws->sum('total_amount'),2) }}</strong></td>
                        <td class="text-right"><strong>${{ number_format($withdraws->sum('calculated_charge'),2) }}</strong></td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{-- Print Script --}}
<script>
function printReport() {
  var printContents = document.getElementById('printArea').innerHTML;
  var printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Sales & Charges Report - {{ config('app.name') }}</title>
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
      <h3 class="text-center mb-4">Sales & Charges Report</h3>
      <p class="text-center">Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
      ${printContents}
    </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.onload = function() {
    printWindow.focus();
    printWindow.print();
    printWindow.onafterprint = function() { printWindow.close(); };
  };
}

// Auto-update date fields and submit
document.addEventListener('DOMContentLoaded', function() {
  function formatDateLocal(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  document.querySelector('#reportType').addEventListener('change', function() {
    const type = this.value;
    let from, to;
    const today = new Date();

    if (type === 'daily') {
        from = to = formatDateLocal(today);
    } else if (type === 'monthly') {
        const startMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const endMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        from = formatDateLocal(startMonth);
        to = formatDateLocal(endMonth);
    } else if (type === 'yearly') {
        const startYear = new Date(today.getFullYear(), 0, 1);
        const endYear = new Date(today.getFullYear(), 11, 31);
        from = formatDateLocal(startYear);
        to = formatDateLocal(endYear);
    }

    document.querySelector('#fromDate').value = from;
    document.querySelector('#toDate').value = to;

    // auto submit
    document.getElementById('reportForm').submit();
  });
});

</script>
@endsection
