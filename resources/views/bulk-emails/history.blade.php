<!-- resources/views/bulk-emails/history.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Email History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

@include('partials.header')

<div class="flex flex-grow">
    @include('partials.sidebar')

    <main class="flex-grow p-8">
        <!-- Title & Back Button -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Bulk Email History</h2>
            <a href="{{ route('bulk-emails.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
               ← Back to Bulk Emails
            </a>
        </div>

        @if($bulkEmails->isEmpty())
            <p>No bulk emails sent yet.</p>
        @else
            <div class="bg-white shadow-md rounded-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-left">Date Sent</th>
                            <th class="py-3 px-4 text-left">Template</th>
                            <th class="py-3 px-4 text-left">Recipients</th>
                            <th class="py-3 px-4 text-left">Emails Sent</th>
                            <th class="py-3 px-4 text-left">Variables</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bulkEmails as $email)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-4">
                                {{ $email->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $email->template_name }}
                            </td>
                            <td class="py-3 px-4">
                                @foreach(json_decode($email->recipient_lists) as $list)
                                    {{ ucwords(str_replace('_',' ', $list)) }}<br>
                                @endforeach
                            </td>
                            <td class="py-3 px-4">
                                {{ $email->emails_sent }}
                            </td>
                            <td class="py-3 px-4">
                                <pre class="text-sm leading-snug">
{{ json_encode(json_decode($email->variables), JSON_PRETTY_PRINT) }}
                                </pre>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>
</div>

</body>
</html>