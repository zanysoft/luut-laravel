<?php
$action = $action ?? null;
$dropdown = $dropdown ?? false;
$attributes = $attributes ?? [];
$icon = $icon ?? null;
$label = $label ?? null;

$modal = $modal ?? $attributes['modal'] ?? false;

if ($modal && !isset($attributes['data-toggle'])) {
    if (!isset($attributes['data-toggle'])) {
        $attributes['data-toggle'] = 'modal';
    }

    if (is_string($modal) && \Str::startsWith($modal, '#')) {
        $attributes['data-target'] = $modal;
    }

    if (!isset($attributes['data-remote'])) {
        $attributes['data-remote'] = $url;
    }

    $url = 'javascript:void(0);';
}

if (isset($attributes['title'])) {
    $attributes['title'] = \Str::title($attributes['title']);
}

$class = isset($class) ? $class : '';
if (isset($attributes['class'])) {
    $class .= ' ' . $attributes['class'];
    unset($attributes['class']);
}

$is_disabled = isset($disabled) && $disabled ? true : false;
if (!$is_disabled && (array_key_exists('disabled', $attributes) || in_array('disabled', $attributes))) {
    $is_disabled = $attributes['disabled'] ?? true;
}

if ($is_disabled) {
    $url = 'javascript:void(0);';
    if (!in_array('disabled', $attributes) && !array_key_exists('disabled', $attributes)) {
        $attributes[] = 'disabled';
    }
    if ($action == 'delete') {
        $attributes['title'] = __("You can't delete");
    }
} else {
    if ($action == 'delete') {
        if (!array_key_exists('data-method', $attributes)) {
            $attributes['data-method'] = "post";
        }
        if (!array_key_exists('data-confirm', $attributes)) {
            $attributes['data-confirm'] = __('Are you sure you want to delete this record?');
        }
    }
}

$_attrs = '';
foreach ($attributes as $attribute => $value) {
    $boolean_attr = ['disabled', 'readonly'];
    if (is_string($attribute)) {
        if (in_array(strtolower($attribute), $boolean_attr, true)) {
            if ($value !== false && $value != 0) {
                $_attrs .= ' ' . $attribute;
            }
        } else {
            $_attrs .= ' ' . $attribute . '="' . $value . '"';
        }
    } elseif (is_string($value)) {
        $_attrs .= ' ' . $value;
    }
}

$url = $url ? $url : 'javascript:void(0);';
?>
@if(in_array($action,['delete','destroy','remove']))
    @if($dropdown)
        <a href="{{ $url }}" class="dropdown-item {{trim($class)}}" {!! $_attrs !!}><i class="fa fa-trash"></i> {{ $label ? $label: __('Delete') }}</a>
    @else
        <a href="{{ $url }}" class="btn btn-xs btn-danger {{trim($class)}}" {!! $_attrs !!}><i class="fa fa-trash"></i> {{ $label ? $label: __('Delete') }}</a>
    @endif
@elseif(in_array($action,['edit','update','modify']))
    @if($dropdown)
        <a href="{{ $url }}" class="dropdown-item {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-edit"></i> {{ $label ? $label: __('Edit') }}</a>
    @else
        <a href="{{ $url }}" class="btn btn-xs btn-primary {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-edit"></i> {{ $label ? $label: __('Edit') }}</a>
    @endif
@elseif(in_array($action,['show','preview','view']))
    @if($dropdown)
        <a href="{{ $url }}" class="dropdown-item {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-eye"></i> {{ $label ? $label: __('View') }}</a>
    @else
        <a href="{{ $url }}" class="btn btn-xs btn-info {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-eye"></i> {{ $label ? $label: __('View') }}</a>
    @endif
@elseif(in_array($action,['send','resend']))
    @if($dropdown)
        <a href="{{ $url }}" class="dropdown-item {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-envelope"></i> {{ $label ? $label: title_case($action) }}</a>
    @else
        <a href="{{ $url }}" class="btn btn-xs btn-info {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-envelope"></i> {{ $label ? $label: title_case($action) }}</a>
    @endif
@elseif($action)
    @if($dropdown)
        <a href="{{ $url }}" class="dropdown-item {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-{{$icon?$icon:'circle'}}"></i> {{ $label ? $label: title_case($action) }}</a>
    @else
        <a href="{{ $url }}" class="btn btn-xs btn-info {{trim($class)}}" {!! $_attrs !!} ><i class="fa fa-{{$icon?$icon:'circle'}}"></i> {{ $label ? $label: title_case($action) }}</a>
    @endif
@endif
