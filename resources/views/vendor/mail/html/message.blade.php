@inject('tenancy', 'tenancy')
@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url'), 'isImage' => (bool)$tenancy->getConfigurationValue('logo1'), 'displayName' => $tenancy->getConfigurationValue('display_name') ])
{{ $tenancy->getConfigurationValue('logo1') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer', ['tenantName' => $tenancy->getConfigurationValue('display_name')])
**{{ $tenancy->getConfigurationValue('display_name') }}**

{{ $tenancy->getConfigurationValue('contact_address') }},
{{ $tenancy->getConfigurationValue('contact_city') }},
{{ $tenancy->getConfigurationValue('contact_state') }} {{ $tenancy->getConfigurationValue('zip') }}

{{ $tenancy->getConfigurationValue('contact_phone') }} | {{ $tenancy->getConfigurationValue('contact_email') }}

[{{ $tenancy->getConfigurationValue('contact_url') }}]({{ $tenancy->getConfigurationValue('contact_url') }})
@endcomponent
@endslot
@endcomponent
