@extends('admin.main')

@section('css_sheets')
    <style>
        .form-card .card-header {
            background: #fff;
            border-bottom: 0;
        }

        .form-card .required:after {
            content: " *";
            color: #dc3545;
        }

        .row-mini {
            border-bottom: 1px solid #eee;
            padding-bottom: .5rem;
            margin-bottom: .5rem;
        }

        .remove-btn {
            cursor: pointer;
            color: #dc3545;
        }

        .badge-key {
            background: #f5f5f5;
            color: #555;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Site Settings</h4>
            <a href="{{ route('voyager.site_settings.index') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Alerts --}}
        @if(session('success') || session('message'))
            <div class="alert alert-success">{{ session('success') ?? session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('voyager.site_settings.update', $settings->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Contact --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Top Bar Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Emails --}}
                        <div class="col-md-6">
                            <label class="form-label">Emails</label>
                            <div id="emailsContainer">
                                @php
                                    $emails = old('emails', $settings->emails ?? []);
                                    if (empty($emails)) $emails = [''];
                                @endphp
                                @foreach($emails as $i => $email)
                                    <div class="row row-mini g-2 align-items-end">
                                        <div class="col-10">
                                            <input type="text" name="emails[{{ $i }}]" value="{{ $email }}"
                                                   class="form-control" placeholder="info@example.com" maxlength="190">
                                        </div>
                                        <div class="col-2 text-end">
                                            <span class="remove-btn small">Remove</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addEmailRow">
                                <i class="bx bx-plus"></i> Add Email
                            </button>
                        </div>

                        {{-- Phones --}}
                        <div class="col-md-6">
                            <label class="form-label">Phones</label>
                            <div id="phonesContainer">
                                @php
                                    $phones = old('phones', $settings->phones ?? []);
                                    if (empty($phones)) $phones = [''];
                                @endphp
                                @foreach($phones as $i => $phone)
                                    <div class="row row-mini g-2 align-items-end">
                                        <div class="col-10">
                                            <input type="text" name="phones[{{ $i }}]" value="{{ $phone }}"
                                                   class="form-control" placeholder="+971 55 000 0000" maxlength="190">
                                        </div>
                                        <div class="col-2 text-end">
                                            <span class="remove-btn small">Remove</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addPhoneRow">
                                <i class="bx bx-plus"></i> Add Phone
                            </button>
                        </div>

                        {{-- Address --}}
                        <div class="col-md-6">
                            <label class="form-label">Address Line</label>
                            <input type="text" name="address_line" class="form-control"
                                   value="{{ old('address_line', $settings->address_line) }}" maxlength="255"
                                   placeholder="Office 123, Street, District">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control"
                                   value="{{ old('city', $settings->city) }}" maxlength="120">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control"
                                   value="{{ old('country', $settings->country) }}" maxlength="120">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Socials --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Social Links</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp URL</label>
                        <input type="text" name="whatsapp_url" class="form-control"
                               value="{{ old('whatsapp_url', $settings->whatsapp_url) }}" maxlength="255"
                               placeholder="https://wa.me/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Facebook URL</label>
                        <input type="text" name="facebook_url" class="form-control"
                               value="{{ old('facebook_url', $settings->facebook_url) }}" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" name="instagram_url" class="form-control"
                               value="{{ old('instagram_url', $settings->instagram_url) }}" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn URL</label>
                        <input type="text" name="linkedin_url" class="form-control"
                               value="{{ old('linkedin_url', $settings->linkedin_url) }}" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube URL</label>
                        <input type="text" name="youtube_url" class="form-control"
                               value="{{ old('youtube_url', $settings->youtube_url) }}" maxlength="255">
                    </div>
                </div>
            </div>

            {{-- Portfolio CTA --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Portfolio CTA</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label">CTA Text</label>
                        <input type="text" name="portfolio_cta_text" class="form-control"
                               value="{{ old('portfolio_cta_text', $settings->portfolio_cta_text) }}" maxlength="120"
                               placeholder="Get Our Portfolio">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">CTA URL</label>
                        <input type="text" name="portfolio_cta_url" class="form-control"
                               value="{{ old('portfolio_cta_url', $settings->portfolio_cta_url) }}" maxlength="255"
                               placeholder="/files/portfolio.pdf or https://...">
                    </div>
                </div>
            </div>

            {{-- Footer About --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Footer About</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="footer_about_title" class="form-control"
                                   value="{{ old('footer_about_title', $settings->footer_about_title) }}"
                                   maxlength="190">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Text</label>
                            <textarea name="footer_about_text" class="form-control"
                                      rows="4">{{ old('footer_about_text', $settings->footer_about_text) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Branding --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Branding</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Logo Path</label>
                        <input type="text" name="logo_path" class="form-control"
                               value="{{ old('logo_path', $settings->logo_path) }}" maxlength="255"
                               placeholder="/uploads/logo.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon Path</label>
                        <input type="text" name="favicon_path" class="form-control"
                               value="{{ old('favicon_path', $settings->favicon_path) }}" maxlength="255"
                               placeholder="/uploads/favicon.ico">
                    </div>
                </div>
            </div>

            {{-- Footer Links --}}
            <div class="card form-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Footer Links</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addFooterLinkRow">
                        <i class="bx bx-plus"></i> Add Link
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-2 small text-muted">Groups help position links under headings (e.g., <span
                                class="badge badge-key">useful</span>, <span class="badge badge-key">services</span>,
                        <span class="badge badge-key">projects</span>).
                    </div>
                    <div id="footerLinksContainer">
                        @php
                            $flat = [];
                            if (old('footer_links')) {
                                $flat = old('footer_links');
                            } else {
                                foreach(($footerLinks ?? []) as $group => $items) {
                                    foreach($items as $item) {
                                        $flat[] = [
                                            'id' => $item->id,
                                            'label' => $item->label,
                                            'url' => $item->url,
                                            'group_key' => $item->group_key,
                                            'display_order' => $item->display_order,
                                            'is_active' => $item->is_active,
                                        ];
                                    }
                                }
                            }
                        @endphp
                        @forelse($flat as $i => $row)
                            <div class="row row-mini g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Label</label>
                                    <input type="text" name="footer_links[{{ $i }}][label]"
                                           value="{{ $row['label'] ?? '' }}" class="form-control" maxlength="190">
                                    <input type="hidden" name="footer_links[{{ $i }}][id]"
                                           value="{{ $row['id'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">URL</label>
                                    <input type="text" name="footer_links[{{ $i }}][url]"
                                           value="{{ $row['url'] ?? '' }}" class="form-control" maxlength="255">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Group</label>
                                    <input type="text" name="footer_links[{{ $i }}][group_key]"
                                           value="{{ $row['group_key'] ?? 'useful' }}" class="form-control"
                                           maxlength="50" placeholder="useful">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="footer_links[{{ $i }}][display_order]"
                                           value="{{ $row['display_order'] ?? 0 }}" class="form-control" min="0">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Active</label>
                                    <input type="checkbox" name="footer_links[{{ $i }}][is_active]" value="1"
                                           class="form-check-input" @checked(!empty($row['is_active']))>
                                </div>
                                <div class="col-12 text-end">
                                    <span class="remove-btn small">Remove</span>
                                </div>
                            </div>
                        @empty
                            <div class="row row-mini g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Label</label>
                                    <input type="text" name="footer_links[0][label]" class="form-control"
                                           maxlength="190">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">URL</label>
                                    <input type="text" name="footer_links[0][url]" class="form-control" maxlength="255">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Group</label>
                                    <input type="text" name="footer_links[0][group_key]" class="form-control"
                                           maxlength="50" placeholder="useful">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="footer_links[0][display_order]" class="form-control"
                                           value="0" min="0">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Active</label>
                                    <input type="checkbox" name="footer_links[0][is_active]" value="1"
                                           class="form-check-input" checked>
                                </div>
                                <div class="col-12 text-end">
                                    <span class="remove-btn small">Remove</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('voyager.site_settings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helpers
            function nextIdx(container) {
                return container.querySelectorAll('.row-mini').length;
            }

            // Emails
            const emailsContainer = document.getElementById('emailsContainer');
            document.getElementById('addEmailRow').addEventListener('click', function () {
                const i = nextIdx(emailsContainer);
                emailsContainer.insertAdjacentHTML('beforeend', `
            <div class="row row-mini g-2 align-items-end">
                <div class="col-10">
                    <input type="text" name="emails[${i}]" class="form-control" placeholder="info@example.com" maxlength="190">
                </div>
                <div class="col-2 text-end"><span class="remove-btn small">Remove</span></div>
            </div>
        `);
            });

            // Phones
            const phonesContainer = document.getElementById('phonesContainer');
            document.getElementById('addPhoneRow').addEventListener('click', function () {
                const i = nextIdx(phonesContainer);
                phonesContainer.insertAdjacentHTML('beforeend', `
            <div class="row row-mini g-2 align-items-end">
                <div class="col-10">
                    <input type="text" name="phones[${i}]" class="form-control" placeholder="+971 55 000 0000" maxlength="190">
                </div>
                <div class="col-2 text-end"><span class="remove-btn small">Remove</span></div>
            </div>
        `);
            });

            // Footer links
            const linksContainer = document.getElementById('footerLinksContainer');
            document.getElementById('addFooterLinkRow').addEventListener('click', function () {
                const i = nextIdx(linksContainer);
                linksContainer.insertAdjacentHTML('beforeend', `
            <div class="row row-mini g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Label</label>
                    <input type="text" name="footer_links[${i}][label]" class="form-control" maxlength="190">
                </div>
                <div class="col-md-4">
                    <label class="form-label">URL</label>
                    <input type="text" name="footer_links[${i}][url]" class="form-control" maxlength="255">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Group</label>
                    <input type="text" name="footer_links[${i}][group_key]" class="form-control" maxlength="50" value="useful">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="footer_links[${i}][display_order]" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Active</label>
                    <input type="checkbox" name="footer_links[${i}][is_active]" value="1" class="form-check-input" checked>
                </div>
                <div class="col-12 text-end">
                    <span class="remove-btn small">Remove</span>
                </div>
            </div>
        `);
            });

            // Remove handlers (delegated)
            document.body.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-btn')) {
                    const row = e.target.closest('.row-mini');
                    if (row) row.remove();
                }
            });
        });
    </script>
@endsection
