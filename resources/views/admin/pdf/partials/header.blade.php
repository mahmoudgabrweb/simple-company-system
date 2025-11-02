<style>
    @page { margin: 130px 25px 110px 25px; }
    .pdf-header,.pdf-footer { position: fixed; left:0; right:0; color:#111; }
    .pdf-header { top:-90px; height:110px; }
    .pdf-footer { bottom:-80px; height:90px; }

    .hdr-wrap { display:flex; align-items:center; justify-content:space-between;
        border-bottom:2px solid #e5e7eb; padding:18px 0 12px; font-family: DejaVu Sans, sans-serif; }
    .hdr-left { display:flex; align-items:center; gap:12px; }
    .hdr-left img { height:32px; }
    .hdr-left .brand-name { font-weight:700; letter-spacing:2px; color:#1984b3; font-size:18px; }
    .hdr-right { background:#1DA3BD; color:#fff; font-weight:700; font-size:16px; padding:10px 16px; border-radius:4px; }

    .ftr-wrap { border-top:2px solid #e5e7eb; padding:10px 0; font-size:11px;
        display:flex; align-items:center; justify-content:space-between; }
    .ftr-center { font-weight:700; letter-spacing:2px; }
    .page-num:before { content: counter(page) " / " counter(pages); }
</style>

<div class="pdf-header">
    <div class="hdr-wrap">
        <div class="hdr-left">
            @if(is_file($branding['logo'] ?? ''))
                <img src="{{ $branding['logo'] }}" alt="Logo">
            @endif
            <div class="brand-name">{{ $branding['brand'] ?? '' }}</div>
        </div>
        <div class="hdr-right">{{ $branding['doc_title'] ?? 'AGREEMENT' }}</div>
    </div>
</div>

<div class="pdf-footer">
    <div class="ftr-wrap">
        <div>{{ optional($quotation->created_at)->format('m/d/Y') }}</div>
        <div class="ftr-center">{{ $branding['brand'] ?? '' }} | {{ $branding['website'] ?? '' }}</div>
        <div>Page <span class="page-num"></span></div>
    </div>
</div>
