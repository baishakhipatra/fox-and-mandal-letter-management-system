<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Confirmation Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
        }
        .line {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="title">DELIVERY CONFIRMATION REPORT</div>

    <div class="line"><span class="label">Letter ID:</span> {{ $letter->letter_id }}</div>
    <div class="line"><span class="label">Received From:</span> {{ ucwords($letter->received_from) }}</div>
    {{-- <div class="line">
        <span class="label">Send To (Member/Team):</span>
        @if(optional($letter->delivery)?->deliveredToUser)
            {{ optional($letter->delivery->deliveredToUser)->name }}
            @if(optional($letter->delivery->deliveredToUser->team)->isNotEmpty())
                ({{ optional($letter->delivery->deliveredToUser->team)->pluck('name')->join(', ') }})
            @endif
        @else
            {{ $letter->send_to ?? 'Not specified' }}
        @endif
    </div> --}}
    <div class="line">
        <span class="label">Send To (Member/Team):</span>
        @php
            use Illuminate\Support\Str;

            $sendTo = $letter->send_to;
            $sendToName = 'Not specified';

            if (Str::startsWith($sendTo, 'member_')) {
                $memberId = Str::after($sendTo, 'member_');
                $member = \App\Models\User::find($memberId);
                $sendToName = $member ? ucwords($member->name) . ' (Member)' : 'Unknown Member';
            } elseif (Str::startsWith($sendTo, 'team_')) {
                $teamId = Str::after($sendTo, 'team_');
                $team = \App\Models\Team::find($teamId);
                $sendToName = $team ? ucwords($team->name) . ' (Team)' : 'Unknown Team';
            }
        @endphp
        {{ $sendToName }}
    </div>
    <div class="line"><span class="label">Document Type:</span> Incoming</div>
    <div class="line"><span class="label">Document Reference No:</span> {{ $letter->document_reference_no }}</div>
    <div class="line"><span class="label">Document Date:</span>{{ $letter->document_date ? ',' . date('d-m-Y',strtotime($letter->document_date)) : '' }}</div>
    <div class="line"><span class="label">Subject/Document Name:</span> {{ ucwords($letter->subject ?? 'N/A') }}</div>
    <div class="line"><span class="label">Handed Over By:</span>{{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>
    <div class="line"><span class="label">Delivered To:</span>{{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}</div>
    <div class="line"><span class="label">Created:</span> {{ \Carbon\Carbon::parse($letter->created_at)->format('n/j/Y g:i:s A') }}</div>
    <div class="line"><span class="label">Status:</span> {{ ucfirst($letter->status) }}</div>
    <div class="line"><span class="label">Generated on:</span> {{ now()->format('n/j/Y g:i:s A') }}</div>

</body>
</html>

