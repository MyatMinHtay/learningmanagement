@props(['name'])

@php
    $errorsForWildcard = collect($errors->getMessages())
        ->filter(function ($_, $key) use ($name) {
            $pattern = str_replace('\*', '[0-9]+', preg_quote($name));
            return preg_match('/^' . $pattern . '$/', $key);
        });
@endphp

@if ($errorsForWildcard->isNotEmpty())
    @foreach ($errorsForWildcard as $key => $messages)
        @php
            preg_match('/^([a-zA-Z_]+)\.([0-9]+)\.([a-zA-Z_]+)$/', $key, $matches);
            $moduleNum = isset($matches[2]) ? $matches[2] + 1 : '?';
            $fieldName = isset($matches[3]) ? ucfirst(str_replace('_', ' ', $matches[3])) : 'Field';
        @endphp
        @foreach ($messages as $message)
            <p class="text-danger col-12 errormessage">Module {{ $moduleNum }} {{ $fieldName }} is required.</p>
        @endforeach
    @endforeach
@endif
