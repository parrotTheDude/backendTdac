<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Email Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

@include('partials.header')
<div class="flex flex-grow">
    @include('partials.sidebar')

    <main class="flex-grow p-8">
        <h2 class="text-2xl font-bold mb-4">Send Bulk Email</h2>

        @if(session('status'))
            <div class="mb-4 text-green-600 font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- If there's a 'bulk_email_id' in the query, show the top box & loading bar -->
        @if(request('bulk_email_id'))
            <div id="sendingBox" class="bg-blue-100 border border-blue-300 p-4 rounded mb-4">
                <p id="sendingStatus" class="text-blue-700 font-medium">
                    Sending in progress...
                </p>
                <!-- loading bar container -->
                <div id="loadingBar" class="w-full bg-gray-200 rounded-full h-4 mt-2">
                    <div id="loadingProgress" class="bg-indigo-500 h-4 rounded-full" style="width: 0%;"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Emails Sent: <span id="emailsSentCount">0</span>
                    /
                    <span id="emailsTotalCount">{{ request('total') ?? 0 }}</span>
                </p>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-md p-6">
            <form id="bulkEmailForm">
                @csrf

                <!-- optional test email -->
                <div class="mb-4">
                    <label class="block font-medium mb-1" for="testEmail">
                        Send Test Email (optional):
                    </label>
                    <input
                        type="email"
                        name="testEmail"
                        id="testEmail"
                        class="w-full border rounded p-2"
                        placeholder="yourname@example.com"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                        If provided, only one test email will be sent.
                    </p>
                </div>

                <!-- Recipient list -->
                <div class="mb-6">
                    <label class="block font-medium mb-2">
                        Select Recipient List:
                    </label>
                    <select name="recipient_list" required class="w-full border rounded p-2">
                        <option value="" disabled selected>Choose a subscription list</option>
                        @foreach($lists as $list)
                            <option value="{{ $list->list_name }}">
                                {{ ucwords(str_replace('-', ' ', $list->list_name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Template selection -->
                <div class="mb-4">
                    <label class="block font-medium mb-2">
                        Select Email Template:
                    </label>
                    <select name="template_id" id="templateSelect" required class="w-full border rounded p-2">
                        @foreach($templatesWithDetails as $template)
                            <option
                                value="{{ $template['id'] }}"
                                data-variables="{{ implode(',', $template['variables']) }}"
                            >
                                {{ $template['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="template_name" id="template_name" />
                </div>

                <!-- Variables: auto-hide if none -->
                <div class="mb-4" id="varsSection">
                    <label class="block font-medium mb-2">Fill Template Variables:</label>
                    <div id="variablesContainer" class="space-y-2 p-3 bg-gray-100 rounded">
                        No variables required.
                    </div>
                </div>

                <button type="submit"
                        class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600 transition">
                    Send Bulk Email
                </button>
            </form>
        </div>

        <hr class="my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Recent Bulk Email History</h3>
            <a href="{{ route('bulk-emails.history') }}"
               class="text-indigo-600 hover:underline text-sm">
               View All
            </a>
        </div>

        @if($bulkEmails->isEmpty())
            <p>No bulk emails sent yet.</p>
        @else
            @php
                // Show only last 5
                $recentEmails = $bulkEmails->take(5);
            @endphp

            <table class="w-full bg-white shadow-md rounded overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left">Date Sent</th>
                        <th class="py-3 px-4 text-left">Template</th>
                        <th class="py-3 px-4 text-left">Recipients</th>
                        <th class="py-3 px-4 text-left">Sent Emails</th>
                        <th class="py-3 px-4 text-left">Variables</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recentEmails as $email)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-4">
                            {{ $email->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            {{ $email->template_name }}
                        </td>
                        <td class="py-3 px-4">
                            @foreach(json_decode($email->recipient_lists) as $lst)
                                {{ ucwords(str_replace('-', ' ', $lst)) }}<br>
                            @endforeach
                        </td>
                        <td class="py-3 px-4">
                            {{ $email->emails_sent }}
                        </td>
                        <td class="py-3 px-4">
                            <pre class="text-xs">
                                {{ json_encode(json_decode($email->variables), JSON_PRETTY_PRINT) }}
                            </pre>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form           = document.getElementById('bulkEmailForm');
    const templateSelect = document.getElementById('templateSelect');
    const templateName   = document.getElementById('template_name');
    const varsSection    = document.getElementById('varsSection');
    const varsContainer  = document.getElementById('variablesContainer');

    function updateTemplateName() {
        const selectedOption = templateSelect.options[templateSelect.selectedIndex];
        templateName.value = selectedOption.textContent.trim();
    }

    function renderTemplateVars() {
        const selectedOption = templateSelect.options[templateSelect.selectedIndex];
        const rawVars = selectedOption.dataset.variables || '';
        const vars = rawVars.split(',')
            .map(v => v.trim())
            .filter(v => v && !v.includes('pm:unsubscribe'));

        varsContainer.innerHTML = '';

        if (!vars.length) {
            varsSection.classList.add('hidden');
            return;
        }
        varsSection.classList.remove('hidden');

        vars.forEach(variable => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <label class="block font-medium text-sm mb-1">${variable}:</label>
                <input
                    type="text"
                    name="variables[${variable}]"
                    class="w-full border p-2 rounded"
                    placeholder="Enter ${variable}"
                    required
                />
            `;
            varsContainer.appendChild(wrapper);
        });
    }

    // Initialize template name and variables on load
    updateTemplateName();
    renderTemplateVars();

    templateSelect.addEventListener('change', () => {
        updateTemplateName();
        renderTemplateVars();
    });

    // Single confirm + reload for progress bar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to send this bulk email?')) return;

        const formData = new FormData(this);

        fetch('{{ route('bulk-emails.send') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            // data should have { bulk_email_id, total }
            const id = data.bulk_email_id;
            const total = data.total || 0;
            // Reload page with query params
            window.location.href = '?bulk_email_id=' + id + '&total=' + total;
        })
        .catch(err => {
            alert('Error sending bulk email.');
            console.error(err);
        });
    });

    // If we have a bulk_email_id in the query, poll for progress
    const urlParams       = new URLSearchParams(window.location.search);
    const bulkEmailId     = urlParams.get('bulk_email_id');
    const totalRecipients = parseInt(urlParams.get('total') || '0', 10);

    if (bulkEmailId) {
        pollProgress(bulkEmailId, totalRecipients);
    }

    function pollProgress(bulkEmailId, total) {
        const sendingBox       = document.getElementById('sendingBox');
        const sendingStatus    = document.getElementById('sendingStatus');
        const loadingBar       = document.getElementById('loadingBar');
        const loadingProgress  = document.getElementById('loadingProgress');
        const emailsSentCount  = document.getElementById('emailsSentCount');
        const emailsTotalCount = document.getElementById('emailsTotalCount');

        if (emailsTotalCount) {
            emailsTotalCount.textContent = total;
        }

        let interval = setInterval(() => {
            fetch(`/bulk-emails/progress/${bulkEmailId}`)
            .then(r => r.json())
            .then(data => {
                const sent = data.emails_sent;
                emailsSentCount.textContent = sent;
                
                if (total > 0) {
                    const pct = Math.min((sent / total) * 100, 100);
                    loadingProgress.style.width = pct + '%';
                    if (pct >= 100) finish();
                }
            })
            .catch(err => {
                console.error('Progress poll error:', err);
            });
        }, 2000);

        function finish() {
            clearInterval(interval);
            // Turn the box green
            sendingBox.classList.remove('bg-blue-100', 'border-blue-300');
            sendingBox.classList.add('bg-green-100', 'border-green-300');
            sendingStatus.classList.remove('text-blue-700');
            sendingStatus.classList.add('text-green-700');
            sendingStatus.textContent = 'Email sending complete!';
        }
    }
});
</script>
</body>
</html>