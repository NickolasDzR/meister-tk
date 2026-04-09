@props(['type' => '', 'class' => '', 'text' => ''])

<button class="{{ trim('button button_' . $type . ' ' . $class) }}">{{ $text }}</button>