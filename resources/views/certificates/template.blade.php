@php
$viewMap = [
    'cp12_homeowner'        => 'certificates.cp12-homeowner',
    'cp12_landlord'         => 'certificates.cp12-landlord',
    'warning_notice'        => 'certificates.warning-notice',
    'installation_checklist'=> 'certificates.installation-checklist',
    'gas_service_record'    => 'certificates.gas-service-record',
    'minor_works'           => 'certificates.minor-works',
    'disconnection'         => 'certificates.disconnection',
    'invoice'               => 'certificates.invoice',
    'quote'                 => 'certificates.quote',
];
$viewName = $viewMap[$certificate->type] ?? 'certificates.cp12-homeowner';
@endphp

@include($viewName, array_merge(['certNo' => $certificate->certificate_number], $certificate->form_data ?? []))
