@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select...',
    'required' => false,
    'onchange' => null,
    'disabled' => false,
])

@php
    $inputId = $id ?? $name;
    $uid     = 'app-dd-' . $inputId . '-' . substr(md5(uniqid()), 0, 6);
    $label   = $placeholder;
    foreach ($options as $val => $lbl) {
        if ((string)$val === (string)$selected) { $label = $lbl; break; }
    }
    $ddOnchange  = ($onchange && !$disabled) ? ' data-onchange="' . e($onchange) . '"'   : '';
    $inputReq    = $required  ? ' required'                               : '';
@endphp

<div {{ $attributes->merge(['class' => 'app-dd' . ($disabled ? ' disabled' : '')]) }} id="{{ $uid }}"{!! $ddOnchange !!}>
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $selected }}"{!! $inputReq !!}{{ $disabled ? ' disabled' : '' }}>
    <div class="dd-trigger" @if(!$disabled) onclick="appDdToggle('{{ $uid }}')" @endif>
        <span id="{{ $uid }}-label">{{ $label }}</span>
        <span class="dd-arrow"><i class="fas fa-chevron-down"></i></span>
    </div>
    <div class="dd-menu">
        @foreach($options as $val => $lbl)
        <div class="dd-item{{ (string)$val === (string)$selected ? ' selected' : '' }}"
             data-value="{{ $val }}"
             @if(!$disabled) onclick="appDdSelect('{{ $uid }}', {{ json_encode((string)$val) }}, {{ json_encode($lbl) }})" @endif>{{ $lbl }}</div>
        @endforeach
    </div>
</div>
