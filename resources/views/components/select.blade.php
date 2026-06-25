@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select...',
    'required' => false,
    'onchange' => null,
])

@php
    $inputId = $id ?? $name;
    $uid     = 'app-dd-' . $inputId . '-' . substr(md5(uniqid()), 0, 6);
    $label   = $placeholder;
    foreach ($options as $val => $lbl) {
        if ((string)$val === (string)$selected) { $label = $lbl; break; }
    }
    $ddOnchange  = $onchange  ? ' data-onchange="' . e($onchange) . '"'   : '';
    $inputReq    = $required  ? ' required'                               : '';
@endphp

<div class="app-dd" id="{{ $uid }}"{!! $ddOnchange !!}>
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $selected }}"{!! $inputReq !!}>
    <div class="dd-trigger" onclick="appDdToggle('{{ $uid }}')">
        <span id="{{ $uid }}-label">{{ $label }}</span>
        <span class="dd-arrow"><i class="fas fa-chevron-down"></i></span>
    </div>
    <div class="dd-menu">
        @foreach($options as $val => $lbl)
        <div class="dd-item{{ (string)$val === (string)$selected ? ' selected' : '' }}"
             data-value="{{ $val }}"
             onclick="appDdSelect('{{ $uid }}', {{ json_encode((string)$val) }}, {{ json_encode($lbl) }})">{{ $lbl }}</div>
        @endforeach
    </div>
</div>
