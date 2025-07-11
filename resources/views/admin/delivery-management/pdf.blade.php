<!DOCTYPE html>
<html>

<head>
    <title>Letter Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
        }

        .signature img {
            max-width: 200px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <h2>Letter Report</h2>

    <div class="info"><span class="label">Letter ID:</span> {{ $letter->letter_id }}</div>
    <div class="info"><span class="label">Status:</span> {{ ucfirst($letter->status) }}</div>
    <div class="info"><span class="label">Received From:</span> {{ ucwords($letter->received_from) }}</div>
    <div class="info"><span class="label">Document Name:</span> {{ ucwords($letter->subject) ?? 'N/A' }}</div>
    {{-- <div class="info"><span class="label">Send To:</span>
        @if($letter->delivery && $letter->delivery->deliveredToUser)
        {{ $letter->delivery->deliveredToUser->name }}
        @if($letter->delivery->deliveredToUser->team->isNotEmpty())
        ({{ $letter->delivery->deliveredToUser->team->pluck('name')->join(', ') }})
        @endif
        @else
        {{ $letter->send_to ?? 'Not specified' }}
        @endif
    </div> --}}
    <div class="info">
        <span class="label">Send To:</span>
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

    <div class="info"><span class="label">Handed Over By:</span> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>
    <div class="info"><span class="label">Reference No:</span> {{ $letter->reference_no ?? 'N/A' }}</div>
    <div class="info"><span class="label">Created:</span>
        {{ \Carbon\Carbon::parse($letter->created_at)->format('m/d/Y') }}</div>
    <div class="info"><span class="label">Delivered Date:</span>
        {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('m/d/Y') : 'Not Delivered' }}
    </div>

    <div class="info"><span class="label">Delivered To:</span> {{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}</div>

    @php
        //$signaturePath = public_path($letter->delivery->signature_image_path);
        $signaturePath = optional($letter->delivery)?->signature_image_path
        ? public_path($letter->delivery->signature_image_path)
        : null;
 
    @endphp

    @if ($letter->delivery && file_exists($signaturePath))
        <div class="signature">
            <div class="label">Signature:</div>
            <img src="{{ $signaturePath }}" style="width: 150px;" alt="Signature">
        </div>
    @endif

</body>

</html>
