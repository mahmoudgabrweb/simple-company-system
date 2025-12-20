<div class="modal-header project-modal-header">
    <h2 class="modal-title">{{ $details->name }}</h2>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body project-modal-body">
    <div class="project-slider-container">
        <div class="owl-carousel owl-fade-slider-one owl-btn-vertical-center owl-dots-bottom-right"
             id="projectSlider">
            @foreach($details->sliders as $slider)
                <div class="item">
                    <img src="{{ Storage::url($slider->image) }}"
                         alt="{{ $slider->caption }}">
                </div>
            @endforeach
        </div>
    </div>
    <div class="project-details">
        <div class="project-description">
            <h3>Project Description</h3>
            <p>{{ $details->full_description }}</p>
        </div>
        <div class="project-features">
            <h4>Key Features</h4>
            <ul>
                @foreach($details->achievements as $achievement)
                    <li>{{ $achievement->description }}</li>
                @endforeach
            </ul>
        </div>
        <div class="project-meta">
            <div class="project-meta-item">
                <strong>Client:</strong>
                <span>{{ $details->client }}</span>
            </div>
            <div class="project-meta-item">
                <strong>Duration:</strong>
                <span>{{ $details->duration }}</span>
            </div>
            <div class="project-meta-item">
                <strong>Category:</strong>
                <span>{{ $details->category }}</span>
            </div>
        </div>
    </div>
</div>