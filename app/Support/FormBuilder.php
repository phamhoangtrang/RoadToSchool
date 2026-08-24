<?php

namespace App\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class FormBuilder
{
    private mixed $model = null;

    public function open(array $options = []): Htmlable
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $htmlMethod = in_array($method, ['GET', 'POST'], true) ? $method : 'POST';
        $action = $this->resolveAction($options);
        $attributes = $this->attributes($options, ['url', 'route', 'action', 'method', 'files']);

        if (! empty($options['files'])) {
            $attributes .= ' enctype="multipart/form-data"';
        }

        $html = '<form method="'.e($htmlMethod).'" action="'.e($action).'"'.$attributes.'>';

        if ($htmlMethod !== 'GET') {
            $html .= csrf_field();
        }

        if ($method !== $htmlMethod) {
            $html .= method_field($method);
        }

        return new HtmlString($html);
    }

    public function model(mixed $model, array $options = []): Htmlable
    {
        $this->model = $model;

        return $this->open($options);
    }

    public function close(): Htmlable
    {
        $this->model = null;

        return new HtmlString('</form>');
    }

    public function label(string $name, ?string $value = null, array $options = []): Htmlable
    {
        $value ??= ucfirst(str_replace('_', ' ', $name));

        return new HtmlString('<label for="'.e($name).'"'.$this->attributes($options).'>'.e($value).'</label>');
    }

    public function text(string $name, mixed $value = null, array $options = []): Htmlable
    {
        return $this->input('text', $name, $value, $options);
    }

    public function hidden(string $name, mixed $value = null, array $options = []): Htmlable
    {
        return $this->input('hidden', $name, $value, $options);
    }

    public function password(string $name, array $options = []): Htmlable
    {
        return $this->input('password', $name, null, $options, false);
    }

    public function file(string $name, array $options = []): Htmlable
    {
        return $this->input('file', $name, null, $options, false);
    }

    public function checkbox(string $name, mixed $value = 1, bool $checked = false, array $options = []): Htmlable
    {
        if ($checked) {
            $options['checked'] = true;
        }

        return $this->input('checkbox', $name, $value ?? 1, $options, false);
    }

    public function textarea(string $name, mixed $value = null, array $options = []): Htmlable
    {
        $value = $this->value($name, $value);

        return new HtmlString(
            '<textarea name="'.e($name).'"'.$this->attributes($options).'>'.e($value).'</textarea>'
        );
    }

    public function select(string $name, iterable $list = [], mixed $selected = null, array $options = []): Htmlable
    {
        $selected = $this->value($name, $selected);
        $placeholder = $options['placeholder'] ?? null;
        unset($options['placeholder']);

        $html = '<select name="'.e($name).'"'.$this->attributes($options).'>';

        if ($placeholder !== null) {
            $html .= '<option value="">'.e($placeholder).'</option>';
        }

        foreach ($list as $value => $label) {
            $isSelected = (string) $value === (string) $selected ? ' selected' : '';
            $html .= '<option value="'.e($value).'"'.$isSelected.'>'.e($label).'</option>';
        }

        return new HtmlString($html.'</select>');
    }

    public function submit(string $value = 'Submit', array $options = []): Htmlable
    {
        return $this->input('submit', '', $value, $options, false, false);
    }

    public function button(string $value = 'Button', array $options = []): Htmlable
    {
        $type = $options['type'] ?? 'button';
        unset($options['type']);

        return new HtmlString('<button type="'.e($type).'"'.$this->attributes($options).'>'.e($value).'</button>');
    }

    private function input(
        string $type,
        string $name,
        mixed $value,
        array $options,
        bool $resolveValue = true,
        bool $includeName = true
    ): Htmlable {
        if ($resolveValue) {
            $value = $this->value($name, $value);
        }

        $nameAttribute = $includeName ? ' name="'.e($name).'"' : '';
        $valueAttribute = $value === null ? '' : ' value="'.e($value).'"';

        return new HtmlString(
            '<input type="'.e($type).'"'.$nameAttribute.$valueAttribute.$this->attributes($options).'>'
        );
    }

    private function value(string $name, mixed $value): mixed
    {
        if (old($name) !== null) {
            return old($name);
        }

        if ($value !== null) {
            return $value;
        }

        return $this->model === null ? null : data_get($this->model, $name);
    }

    private function resolveAction(array $options): string
    {
        if (isset($options['route'])) {
            $route = (array) $options['route'];
            $name = array_shift($route);

            return route($name, $route);
        }

        if (isset($options['action'])) {
            return action($options['action']);
        }

        return $options['url'] ?? url()->current();
    }

    private function attributes(array $options, array $except = []): string
    {
        $html = '';

        foreach ($options as $key => $value) {
            if (in_array($key, $except, true) || $value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $html .= ' '.e($key);
                continue;
            }

            $html .= ' '.e($key).'="'.e($value).'"';
        }

        return $html;
    }
}
