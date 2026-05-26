@extends('hajiri.layouts.app')

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10">
        <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">HR Settings</p>
        <h2 class="text-2xl font-extrabold">Biometric Devices</h2>
        <p class="text-green-200 text-sm mt-1">Manage attendance reader devices and their IP addresses</p>
    </div>
</div>

{{-- Connection Status Banner --}}
<div id="statusBanner"
     class="rounded-2xl px-4 py-3 mb-6 flex items-center gap-3 border bg-amber-50 border-amber-200">
    <div class="shrink-0">
        <svg id="statusSpinner" class="w-5 h-5 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <svg id="statusIconOk" class="w-5 h-5 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <svg id="statusIconErr" class="w-5 h-5 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <span id="statusText" class="text-sm font-semibold text-amber-700">Connecting to HajiriSync…</span>
</div>

{{-- Devices Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Registered Devices</p>
    </div>

    <div id="devicesTableWrapper" class="hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-12">#</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Device Name</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">IP Address</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-24">Port</th>
                    <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-44">Actions</th>
                </tr>
            </thead>
            <tbody id="devicesBody" class="divide-y divide-gray-50"></tbody>
        </table>
    </div>

    <div id="emptyDevices" class="hidden py-14 text-center">
        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
        </svg>
        <p class="text-sm font-semibold text-gray-400">No devices registered yet</p>
        <p class="text-xs text-gray-300 mt-1">Add a device using the form below</p>
    </div>
</div>

{{-- Add Device Form --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6">
    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-4">Add New Device</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input id="addIP_name"
               class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10 transition-colors"
               type="text" placeholder="Device name"/>
        <input id="addIP_ip"
               class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10 transition-colors"
               type="text" placeholder="IP address (e.g. 172.16.0.1)"/>
        <input id="addIP_port"
               class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10 transition-colors"
               type="text" placeholder="Port (e.g. 4370)"/>
    </div>
    <div class="mt-3">
        <button id="addDeviceBtn"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#e2a024] hover:bg-[#c78d1c] text-white text-sm font-extrabold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Add Device
        </button>
    </div>
</div>

{{-- Edit Device Modal --}}
<div x-data="{
        open: false,
        editId: '',
        editName: '',
        editIp: '',
        editPort: ''
     }"
     @open-edit-device.window="
        open = true;
        editId   = $event.detail.id;
        editName = $event.detail.name;
        editIp   = $event.detail.ip;
        editPort = $event.detail.port;
     "
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="open = false">

    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 z-10" @click.stop>
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-0.5">Device Settings</p>
                <h3 class="text-base font-extrabold text-gray-900" x-text="'Edit: ' + editName"></h3>
            </div>
            <button @click="open = false"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Device Name</label>
                <input x-model="editName"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10"
                       type="text" placeholder="Device name"/>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">IP Address</label>
                <input x-model="editIp"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10 font-mono"
                       type="text" placeholder="172.16.0.x"/>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Port</label>
                <input x-model="editPort"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10"
                       type="text" placeholder="4370"/>
            </div>
        </div>

        <div class="flex gap-3 mt-5">
            <button @click="open = false"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button @click="
                    window.dispatchEvent(new CustomEvent('save-edit-device', {
                        detail: { id: editId, name: editName, ip: editIp, port: editPort }
                    }));
                    open = false;
                "
                    class="flex-1 px-4 py-2.5 bg-[#1a5632] text-white text-sm font-bold rounded-xl hover:bg-[#0b2415] transition-colors">
                Save Changes
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    var ws;

    function setStatus(state, text) {
        var banner   = $('#statusBanner');
        var spinner  = $('#statusSpinner');
        var iconOk   = $('#statusIconOk');
        var iconErr  = $('#statusIconErr');
        var statusTxt = $('#statusText');

        // Reset icons
        spinner.addClass('hidden');
        iconOk.addClass('hidden');
        iconErr.addClass('hidden');

        // Reset banner colours
        banner.removeClass(
            'bg-amber-50 border-amber-200 bg-green-50 border-green-200 bg-red-50 border-red-200'
        );
        statusTxt.removeClass('text-amber-700 text-green-700 text-red-700');

        if (state === 'loading') {
            banner.show().addClass('bg-amber-50 border-amber-200');
            statusTxt.addClass('text-amber-700');
            spinner.removeClass('hidden');
        } else if (state === 'success') {
            banner.show().addClass('bg-green-50 border-green-200');
            statusTxt.addClass('text-green-700');
            iconOk.removeClass('hidden');
            setTimeout(function () { banner.fadeOut(400); }, 1800);
        } else if (state === 'error') {
            banner.show().addClass('bg-red-50 border-red-200');
            statusTxt.addClass('text-red-700');
            iconErr.removeClass('hidden');
        }
        statusTxt.text(text);
    }

    function renderDevices(devIPs) {
        var tbody = $('#devicesBody').empty();

        if (!devIPs || devIPs.length === 0) {
            $('#devicesTableWrapper').addClass('hidden');
            $('#emptyDevices').removeClass('hidden');
            return;
        }

        $('#devicesTableWrapper').removeClass('hidden');
        $('#emptyDevices').addClass('hidden');

        devIPs.forEach(function (dev, i) {
            var tr = $(
                '<tr class="hover:bg-gray-50 transition-colors">' +
                    '<td class="px-4 py-3 text-xs font-bold text-gray-400">' + (i + 1) + '</td>' +
                    '<td class="px-4 py-3">' +
                        '<span class="font-semibold text-gray-800">' + $('<span>').text(dev.name).html() + '</span>' +
                    '</td>' +
                    '<td class="px-4 py-3">' +
                        '<span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-mono rounded-lg">' + $('<span>').text(dev.ip).html() + '</span>' +
                    '</td>' +
                    '<td class="px-4 py-3 text-sm text-gray-600">' + dev.port + '</td>' +
                    '<td class="px-4 py-3 text-right">' +
                        '<div class="flex items-center justify-end gap-2">' +
                            '<button class="editButtonIP inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-bold rounded-lg hover:border-[#1a5632] hover:text-[#1a5632] hover:bg-green-50 transition-colors"' +
                                ' data-id="' + dev.id + '" data-name="' + $('<span>').text(dev.name).html() + '" data-ip="' + dev.ip + '" data-port="' + dev.port + '">' +
                                '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                                'Edit' +
                            '</button>' +
                            '<button class="deleteButtonIP inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors"' +
                                ' data-id="' + dev.id + '">' +
                                '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                                'Delete' +
                            '</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );
            tbody.append(tr);
        });
    }

    function connect() {
        if (!("WebSocket" in window)) {
            setStatus('error', 'Your browser does not support WebSocket');
            return;
        }

        ws = new WebSocket("ws://127.0.0.1:5472/devices");

        ws.onopen = function () {
            ws.send(JSON.stringify({ action: "init", name: "null" }));
            setStatus('success', 'Connected to HajiriSync');
        };

        ws.onmessage = function (evt) {
            var response = JSON.parse(evt.data);
            if (!response.status) return;

            if (response.type === 'init') {
                ws.send(JSON.stringify({ action: "listDevices", name: "null" }));
            } else if (response.type === 'listDevices') {
                renderDevices(JSON.parse(response.payload));
            } else if (response.type === 'addDevices') {
                $('#addIP_name').val('');
                $('#addIP_ip').val('');
                $('#addIP_port').val('');
                ws.send(JSON.stringify({ action: "listDevices", name: "null" }));
            } else if (response.type === 'editDevices' || response.type === 'deleteDevices') {
                ws.send(JSON.stringify({ action: "listDevices", name: "null" }));
            }
        };

        ws.onclose = function () {
            setStatus('error', 'HajiriSync is not running — retrying in 3s…');
            $('#devicesTableWrapper').addClass('hidden');
            $('#emptyDevices').addClass('hidden');
            setTimeout(connect, 3000);
        };
    }

    connect();

    // Open edit modal via Alpine custom event
    $(document).on('click', '.editButtonIP', function () {
        window.dispatchEvent(new CustomEvent('open-edit-device', {
            detail: {
                id:   $(this).data('id'),
                name: $(this).data('name'),
                ip:   $(this).data('ip'),
                port: String($(this).data('port'))
            }
        }));
    });

    // Save from Alpine modal → back to WebSocket
    window.addEventListener('save-edit-device', function (e) {
        if (!ws || ws.readyState !== WebSocket.OPEN) return;
        ws.send(JSON.stringify({
            action: "editDevices",
            data: {
                id:   parseInt(e.detail.id),
                name: e.detail.name,
                ip:   e.detail.ip,
                port: parseInt(e.detail.port)
            }
        }));
    });

    // Add device
    $('#addDeviceBtn').on('click', function () {
        var name = $('#addIP_name').val().trim();
        var ip   = $('#addIP_ip').val().trim();
        var port = $('#addIP_port').val().trim();
        if (!name || !ip || !port) return;
        if (!ws || ws.readyState !== WebSocket.OPEN) {
            setStatus('error', 'Not connected — cannot add device');
            return;
        }
        ws.send(JSON.stringify({ action: "addDevices", data: { name: name, ip: ip, port: parseInt(port) } }));
    });

    // Delete device
    $(document).on('click', '.deleteButtonIP', function () {
        if (!confirm('Delete this device?')) return;
        if (!ws || ws.readyState !== WebSocket.OPEN) return;
        ws.send(JSON.stringify({ action: "deleteDevices", data: parseInt($(this).data('id')) }));
    });

});
</script>
@endpush
