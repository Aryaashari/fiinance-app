<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - {{ $period }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; }
        .header h1 { color: #0f172a; font-size: 24px; margin: 0; }
        .header p { color: #64748b; margin: 5px 0 0; }
        .summary { display: flex; margin-bottom: 20px; }
        .summary-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 5px; flex: 1; text-align: center; }
        .summary-card .label { color: #64748b; font-size: 10px; text-transform: uppercase; }
        .summary-card .value { font-size: 18px; font-weight: bold; margin-top: 5px; }
        .income { color: #10b981; }
        .expense { color: #ef4444; }
        .neutral { color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f172a; color: #f8fafc; padding: 10px 8px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) { background: #f8fafc; }
        .section-title { color: #0f172a; font-size: 16px; margin-top: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; }
        .footer { text-align: center; margin-top: 30px; color: #94a3b8; font-size: 10px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>{{ ucfirst($type) }} Report - {{ $period }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="border: 1px solid #e2e8f0; padding: 15px; text-align: center; width: 33%;">
                <div style="color: #64748b; font-size: 10px; text-transform: uppercase;">Total Income</div>
                <div class="income" style="font-size: 18px; font-weight: bold; margin-top: 5px;">
                    Rp {{ number_format($totals['total_income'], 0, ',', '.') }}
                </div>
            </td>
            <td style="border: 1px solid #e2e8f0; padding: 15px; text-align: center; width: 33%;">
                <div style="color: #64748b; font-size: 10px; text-transform: uppercase;">Total Expense</div>
                <div class="expense" style="font-size: 18px; font-weight: bold; margin-top: 5px;">
                    Rp {{ number_format($totals['total_expense'], 0, ',', '.') }}
                </div>
            </td>
            <td style="border: 1px solid #e2e8f0; padding: 15px; text-align: center; width: 33%;">
                <div style="color: #64748b; font-size: 10px; text-transform: uppercase;">Net Cash Flow</div>
                <div class="{{ $totals['net_cash_flow'] >= 0 ? 'income' : 'expense' }}" style="font-size: 18px; font-weight: bold; margin-top: 5px;">
                    Rp {{ number_format($totals['net_cash_flow'], 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <h2 class="section-title">Transaction Details</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Type</th>
                <th class="text-right">Amount</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                <td>{{ $transaction->category->name }}</td>
                <td>
                    <span class="{{ $transaction->category->type === 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($transaction->category->type) }}
                    </span>
                </td>
                <td class="text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                <td>{{ $transaction->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d F Y H:i') }} | Personal Finance Management</p>
    </div>
</body>
</html>
