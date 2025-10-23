@extends('admin.main')

@section('css_sheets')
    <style>
        :root{ --primary:#1D2A3A; --accent:#B89564; --muted:#6C757D; --line:#E0E3E7; }
        .menu-header .hint { color: var(--muted); font-size:.95rem; }
        .saving-indicator{ display:none; gap:.5rem; align-items:center; color:var(--muted); font-size:.95rem; }
        .saving-indicator.active{ display:flex; }
        .dd-placeholder{
            background:rgba(184,149,100,.15);
            border:2px dashed var(--accent);
            border-radius:10px;height:46px;margin:.5rem 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 menu-header">
            <div>
                <h4 class="m-0">
                    <i class="bx bx-list-ul me-1"></i>
                    {{ __('voyager::generic.menu_builder') }} ({{ $menu->name }})
                </h4>
                <div class="hint">{{ __('voyager::menu_builder.drag_drop_info') }}</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                @include('voyager::multilingual.language-selector')
                <div class="saving-indicator" id="savingIndicator">
                    <span class="spinner-border spinner-border-sm"></span>
                    <span>Saving order…</span>
                </div>
                <button type="button" class="btn btn-primary add_item">
                    <i class="bx bx-plus"></i> {{ __('voyager::menu_builder.new_menu_item') }}
                </button>
            </div>
        </div>

        @include('voyager::menus.partial.notice')
        @include('voyager::alerts')

        {{-- Builder --}}
        <div class="card">
            <div class="card-body">
                <div class="dd">
                    {!! menu($menu->name, 'admin', ['isModelTranslatable' => $isModelTranslatable]) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="bx bx-trash me-1"></i> {{ __('voyager::menu_builder.delete_item_question') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"></button>
                </div>
                <div class="modal-footer border-0">
                    <form action="{{ route('voyager.menus.item.destroy', ['menu' => $menu->id, 'id' => '__id']) }}" id="delete_form" method="POST" class="ms-auto">
                        @method('DELETE') @csrf
                        <button type="submit" class="btn btn-danger">{{ __('voyager::menu_builder.delete_item_confirm') }}</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div></div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="menu_item_modal" tabindex="-1" aria-hidden="true" data-multilingual="true">
        <div class="modal-dialog"><div class="modal-content border-0">
                <div class="modal-header border-0">
                    <h5 id="m_hd_add" class="modal-title d-none"><i class="bx bx-plus me-1"></i> {{ __('voyager::menu_builder.create_new_item') }}</h5>
                    <h5 id="m_hd_edit" class="modal-title d-none"><i class="bx bx-edit me-1"></i> {{ __('voyager::menu_builder.edit_item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"></button>
                </div>

                <form id="m_form" method="POST"
                      data-action-add="{{ route('voyager.menus.item.add', ['menu' => $menu->id]) }}"
                      data-action-update="{{ route('voyager.menus.item.update', ['menu' => $menu->id]) }}">
                    <input id="m_form_method" type="hidden" name="_method" value="POST">
                    @csrf

                    <div class="modal-body">
                        @include('voyager::multilingual.language-selector')

                        <div class="mb-3">
                            <label class="form-label" for="m_title">{{ __('voyager::menu_builder.item_title') }}</label>
                            @include('voyager::multilingual.input-hidden', ['_field_name' => 'title', '_field_trans' => ''])
                            <input type="text" class="form-control" id="m_title" name="title" placeholder="{{ __('voyager::generic.title') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="m_link_type">{{ __('voyager::menu_builder.link_type') }}</label>
                            <select id="m_link_type" class="form-select" name="type">
                                <option value="url" selected>{{ __('voyager::menu_builder.static_url') }}</option>
                                <option value="route">{{ __('voyager::menu_builder.dynamic_route') }}</option>
                            </select>
                        </div>

                        <div id="m_url_type" class="mb-3">
                            <label class="form-label" for="m_url">{{ __('voyager::menu_builder.url') }}</label>
                            <input type="text" class="form-control" id="m_url" name="url" placeholder="{{ __('voyager::generic.url') }}">
                        </div>

                        <div id="m_route_type" class="mb-3">
                            <label class="form-label" for="m_route">{{ __('voyager::menu_builder.item_route') }}</label>
                            <input type="text" class="form-control" id="m_route" name="route" placeholder="{{ __('voyager::generic.route') }}">
                            <label class="form-label mt-3" for="m_parameters">{{ __('voyager::menu_builder.route_parameter') }}</label>
                            <textarea rows="3" class="form-control" id="m_parameters" name="parameters"
                                      placeholder="{{ json_encode(['key' => 'value'], JSON_PRETTY_PRINT) }}"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="m_icon_class">
                                {{ __('voyager::menu_builder.icon_class') }}
                                <a href="{{ route('voyager.compass.index') }}#fonts" target="_blank">{!! __('voyager::menu_builder.icon_class2') !!}</a>
                            </label>
                            <input type="text" class="form-control" id="m_icon_class" name="icon_class" placeholder="{{ __('voyager::menu_builder.icon_class_ph') }}">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="m_color">{{ __('voyager::menu_builder.color') }}</label>
                                <input type="color" class="form-control form-control-color" id="m_color" name="color">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="m_target">{{ __('voyager::menu_builder.open_in') }}</label>
                                <select id="m_target" class="form-select" name="target">
                                    <option value="_self" selected>{{ __('voyager::menu_builder.open_same') }}</option>
                                    <option value="_blank">{{ __('voyager::menu_builder.open_new') }}</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <input type="hidden" name="id" id="m_id" value="">
                    </div>

                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary ms-auto">{{ __('voyager::generic.update') }}</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    </div>
                </form>
            </div></div>
    </div>
@endsection

@push('scripts')
    {{-- Voyager’s Nestable (required for drag&drop) --}}
    <script src="{{ asset('vendor/tcg/voyager/assets/js/jquery.nestable.js') }}"></script>

    <script>
        $(function () {
            if (!$.fn.nestable) {
                console.error('Nestable not found. Include vendor/tcg/voyager/assets/js/jquery.nestable.js');
                return;
            }

            const $dd = $('.dd');
            const $saving = $('#savingIndicator');

            $dd.nestable({ expandBtnHTML:'', collapseBtnHTML:'' });

            // Add item
            $('.add_item').on('click', function () {
                const $m = $('#menu_item_modal'), $f = $('#m_form');
                $f.trigger('reset');
                $f.attr('action', $f.data('action-add'));
                $('#m_form_method').val('POST');
                $('#m_hd_add').removeClass('d-none'); $('#m_hd_edit').addClass('d-none');
                $('#m_target').val('_self').trigger('change');
                $('#m_link_type').val('url').trigger('change');
                $('#m_id').val('');
                $m.modal('show');
            });

            // Edit
            $('.item_actions').on('click', '.edit', function (e) {
                const src = $(e.currentTarget), id = src.data('id');
                const $m = $('#menu_item_modal'), $f = $('#m_form');

                $f.attr('action', $f.data('action-update'));
                $('#m_form_method').val('PUT');
                $('#m_hd_add').addClass('d-none'); $('#m_hd_edit').removeClass('d-none');

                $('#m_title').val(src.data('title') || '');
                $('#m_url').val(src.data('url') || '');
                $('#m_route').val(src.data('route') || '');
                $('#m_parameters').val(JSON.stringify(src.data('parameters') || {}));
                $('#m_icon_class').val(src.data('icon_class') || '');
                $('#m_color').val(src.data('color') || '');
                $('#m_id').val(id);

                $('#m_target').val(src.data('target') === '_blank' ? '_blank' : '_self').trigger('change');

                if ((src.data('route') || '') !== '') {
                    $('#m_link_type').val('route').trigger('change');
                    $('#m_url_type').hide(); $('#m_route_type').show();
                } else {
                    $('#m_link_type').val('url').trigger('change');
                    $('#m_route_type').hide(); $('#m_url_type').show();
                }

                @if ($isModelTranslatable)
                const i18nInput = $("#title" + id + "_i18n");
                if (i18nInput.length) {
                    $('#title_i18n').val(i18nInput.val());
                    $m.multilingual({
                        form: 'form',
                        transInputs: '#menu_item_modal input[data-i18n=true]',
                        langSelectors: '.language-selector input',
                        editing: true
                    });
                }
                @endif

                $m.modal('show');
            });

            // Delete
            $('.item_actions').on('click', '.delete', function (e) {
                const id = $(e.currentTarget).data('id');
                const action = '{{ route('voyager.menus.item.destroy', ['menu' => $menu->id, 'id' => '__id']) }}'.replace('__id', id);
                $('#delete_form').attr('action', action);
                $('#delete_modal').modal('show');
            });

            // Link type toggle
            $('#m_link_type').on('change', function () {
                if ($(this).val() === 'route') { $('#m_url_type').hide(); $('#m_route_type').show(); }
                else { $('#m_route_type').hide(); $('#m_url_type').show(); }
            });

            // Reorder (debounced)
            let t=null;
            $dd.on('change', function () {
                if (t) clearTimeout(t);
                $saving.addClass('active');
                t=setTimeout(function(){
                    $.post('{{ route('voyager.menus.order_item',['menu' => $menu->id]) }}', {
                        order: JSON.stringify($dd.nestable('serialize')),
                        _token: '{{ csrf_token() }}'
                    })
                        .done(() => { window.toastr && toastr.success("{{ __('voyager::menu_builder.updated_order') }}"); })
                        .fail((xhr) => { window.toastr && toastr.error(xhr.responseJSON?.message || 'Failed to save order'); })
                        .always(() => { $saving.removeClass('active'); });
                }, 300);
            });
        });
    </script>
@endpush
