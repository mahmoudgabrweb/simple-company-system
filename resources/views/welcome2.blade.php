<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxe Interiors - Premium Design Studio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        /* Services Section */
        .services {
            padding: 6rem 5%;
            background: #f9fafb;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 4rem;
            font-size: 1.1rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .service-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .service-card p {
            color: #666;
            line-height: 1.8;
        }

        /* Portfolio Section */
        .portfolio {
            padding: 6rem 5%;
            background: white;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            height: 400px;
            cursor: pointer;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .portfolio-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .portfolio-item:hover::before {
            opacity: 1;
        }

        .portfolio-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .portfolio-item:hover .portfolio-img {
            transform: scale(1.1);
        }

        .portfolio-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            color: white;
            z-index: 2;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .portfolio-item:hover .portfolio-overlay {
            transform: translateY(0);
        }

        .portfolio-overlay h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* About Section */
        .about {
            padding: 6rem 5%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .about-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-content p {
            font-size: 1.2rem;
            line-height: 1.8;
            opacity: 0.95;
        }

        /* Contact Section */
        .contact {
            padding: 6rem 5%;
            background: #f9fafb;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        /* Footer */
        footer {
            background: #1f2937;
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        footer p {
            opacity: 0.8;
        }

        /* Project Details Modal */
        .project-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            overflow-y: auto;
            animation: fadeIn 0.3s ease;
        }

        .project-modal.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            max-width: 1200px;
            margin: 4rem auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close-modal {
            position: fixed;
            top: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 2rem;
            color: #333;
            transition: all 0.3s ease;
            z-index: 2001;
        }

        .close-modal:hover {
            transform: rotate(90deg);
            background: #667eea;
            color: white;
        }

        .project-hero-img {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .project-details {
            padding: 3rem;
        }

        .project-header {
            margin-bottom: 2rem;
        }

        .project-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .project-category {
            color: #666;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .project-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
            padding: 2rem;
            background: #f9fafb;
            border-radius: 15px;
        }

        .info-item h4 {
            color: #667eea;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .info-item p {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .project-description {
            margin: 2rem 0;
            line-height: 1.8;
            color: #555;
            font-size: 1.1rem;
        }

        .project-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }

        .gallery-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .gallery-img:hover {
            transform: scale(1.05);
        }

        .project-features {
            margin: 3rem 0;
        }

        .features-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .feature-text h5 {
            margin-bottom: 0.3rem;
            color: #333;
        }

        .feature-text p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 1.5rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .portfolio-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                margin: 2rem 1rem;
            }

            .project-details {
                padding: 2rem 1.5rem;
            }

            .project-title {
                font-size: 2rem;
            }

            .project-hero-img {
                height: 300px;
            }

            .close-modal {
                top: 1rem;
                right: 1rem;
                width: 40px;
                height: 40px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<header>
    <nav>
        <div class="logo">LUXE INTERIORS</div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="hero-content">
        <h1>Elevate Your Space</h1>
        <p>We create stunning interiors that blend luxury, functionality, and timeless elegance to transform your vision
            into reality</p>
        <a href="#contact" class="cta-button">Start Your Project</a>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Comprehensive design solutions tailored to your unique style</p>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🏠</div>
                <h3>Residential Design</h3>
                <p>Transform your home into a personalized sanctuary with our expert residential design services. We
                    focus on creating spaces that reflect your lifestyle.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🏢</div>
                <h3>Commercial Spaces</h3>
                <p>Elevate your business environment with sophisticated commercial interiors that enhance productivity
                    and leave lasting impressions on clients.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">✨</div>
                <h3>Luxury Renovations</h3>
                <p>Breathe new life into existing spaces with our comprehensive renovation services, combining modern
                    aesthetics with functional design.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎨</div>
                <h3>Custom Furniture</h3>
                <p>Discover bespoke furniture pieces designed and crafted specifically for your space, ensuring perfect
                    harmony with your interior vision.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">💡</div>
                <h3>Lighting Design</h3>
                <p>Master the art of ambiance with our innovative lighting solutions that enhance mood, functionality,
                    and architectural features.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🪴</div>
                <h3>Space Planning</h3>
                <p>Optimize your layout with strategic space planning that maximizes functionality while maintaining
                    aesthetic appeal and flow.</p>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio">
    <div class="container">
        <h2 class="section-title">Our Work</h2>
        <p class="section-subtitle">Explore our latest projects and design innovations</p>
        <div class="portfolio-grid">
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800" alt="Modern Living Room"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Living Room</h3>
                    <p>Contemporary elegance meets comfort</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1556912173-46c336c7fd55?w=800" alt="Luxury Bedroom"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Luxury Bedroom Suite</h3>
                    <p>Serene sophistication and relaxation</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800" alt="Executive Office"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Executive Office</h3>
                    <p>Professional style and productivity</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800" alt="Dining Space"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Elegant Dining Space</h3>
                    <p>Where culinary art meets design</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=800" alt="Kitchen Design"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Kitchen</h3>
                    <p>Functionality meets aesthetic beauty</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800" alt="Bathroom Design"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Spa-Like Bathroom</h3>
                    <p>Your personal wellness retreat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="about-content">
        <h2>About Our Studio</h2>
        <p>With over 15 years of experience in luxury interior design, we've transformed hundreds of spaces into
            stunning environments that inspire and delight. Our team of award-winning designers combines creativity with
            technical expertise to deliver exceptional results that exceed expectations. We believe every space tells a
            story, and we're here to help you write yours.</p>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-subtitle">Let's bring your vision to life</p>
        <form class="contact-form">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone">
            </div>
            <div class="form-group">
                <label for="message">Project Details</label>
                <textarea id="message" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Luxe Interiors. All rights reserved. Crafted with passion and precision.</p>
</footer>

<!-- Project Details Modal -->
<div id="projectModal" class="project-modal">
    <div class="close-modal" onclick="closeProject()">×</div>
    <div class="modal-content">
        <img id="modalHeroImg" class="project-hero-img" src="" alt="">
        <div class="project-details">
            <div class="project-header">
                <h2 id="modalTitle" class="project-title"></h2>
                <p id="modalCategory" class="project-category"></p>
            </div>

            <div class="project-info">
                <div class="info-item">
                    <h4>Client</h4>
                    <p id="modalClient"></p>
                </div>
                <div class="info-item">
                    <h4>Location</h4>
                    <p id="modalLocation"></p>
                </div>
                <div class="info-item">
                    <h4>Year</h4>
                    <p id="modalYear"></p>
                </div>
                <div class="info-item">
                    <h4>Size</h4>
                    <p id="modalSize"></p>
                </div>
            </div>

            <div class="project-description">
                <p id="modalDescription"></p>
            </div>

            <div class="project-features">
                <h3 class="features-title">Key Features</h3>
                <div class="features-list" id="modalFeatures">
                </div>
            </div>

            <div class="project-gallery" id="modalGallery">
            </div>
        </div>
    </div>
</div>

<script>
    // Project data
    const projects = [
        {
            id: 1,
            title: "Modern Living Room",
            category: "Residential • Living Space",
            client: "The Anderson Family",
            location: "Beverly Hills, CA",
            year: "2024",
            size: "1,200 sq ft",
            description: "A contemporary transformation of a traditional living space into a modern sanctuary. This project seamlessly blends minimalist aesthetics with warm, inviting elements. We incorporated natural materials, custom lighting solutions, and a carefully curated color palette to create a space that feels both sophisticated and comfortable. The open-plan design maximizes natural light while maintaining distinct functional zones for entertainment and relaxation.",
            heroImg: "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?w=600",
                "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=600",
                "https://images.unsplash.com/photo-1617806118233-18e1de247200?w=600"
            ],
            features: [
                {icon: "💡", title: "Smart Lighting", desc: "Integrated automated lighting system"},
                {icon: "🪟", title: "Floor-to-Ceiling Windows", desc: "Maximized natural light exposure"},
                {icon: "🎨", title: "Custom Art Wall", desc: "Bespoke gallery wall design"},
                {icon: "🛋️", title: "Italian Furniture", desc: "Curated luxury furniture pieces"}
            ]
        },
        {
            id: 2,
            title: "Luxury Bedroom Suite",
            category: "Residential • Private Quarters",
            client: "Confidential",
            location: "Manhattan, NY",
            year: "2024",
            size: "850 sq ft",
            description: "An opulent master bedroom retreat designed for ultimate comfort and tranquility. This project showcases our expertise in creating intimate spaces that exude elegance. We focused on layered textures, ambient lighting, and a soothing color palette inspired by nature. Custom millwork, premium fabrics, and carefully selected artwork come together to create a personal sanctuary that promotes rest and rejuvenation.",
            heroImg: "https://images.unsplash.com/photo-1556912173-46c336c7fd55?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1540518614846-7eded433c457?w=600",
                "https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=600",
                "https://images.unsplash.com/photo-1631889993959-41b4e9c6e3c5?w=600"
            ],
            features: [
                {icon: "🌙", title: "Blackout System", desc: "Automated blackout window treatments"},
                {icon: "🔇", title: "Acoustic Panels", desc: "Sound-dampening wall treatments"},
                {icon: "🛏️", title: "Custom Headboard", desc: "Hand-crafted upholstered centerpiece"},
                {icon: "💎", title: "Luxury Materials", desc: "Silk, velvet, and marble accents"}
            ]
        },
        {
            id: 3,
            title: "Executive Office",
            category: "Commercial • Workspace",
            client: "Tech Innovations Inc.",
            location: "San Francisco, CA",
            year: "2024",
            size: "2,000 sq ft",
            description: "A sophisticated executive office that balances professionalism with modern design sensibilities. This space was crafted to inspire productivity while making a powerful statement. We integrated state-of-the-art technology with timeless design elements, creating an environment that impresses clients and supports executive decision-making. Rich materials, strategic lighting, and ergonomic considerations ensure both form and function excel.",
            heroImg: "https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1497366216548-37526070297c?w=600",
                "https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=600",
                "https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600"
            ],
            features: [
                {icon: "📱", title: "Smart Technology", desc: "Integrated AV and control systems"},
                {icon: "📚", title: "Library Wall", desc: "Floor-to-ceiling custom bookshelf"},
                {icon: "🪑", title: "Ergonomic Design", desc: "Premium executive furniture"},
                {icon: "🖼️", title: "Art Collection", desc: "Curated contemporary artwork"}
            ]
        },
        {
            id: 4,
            title: "Elegant Dining Space",
            category: "Residential • Dining Room",
            client: "The Chen Family",
            location: "Miami, FL",
            year: "2023",
            size: "600 sq ft",
            description: "A refined dining room that celebrates the art of gathering and entertaining. This design creates an atmosphere of understated luxury through carefully chosen materials and proportions. A statement chandelier serves as the focal point, while custom millwork and sophisticated color choices create depth and interest. The space seamlessly connects to the kitchen while maintaining its own distinct character.",
            heroImg: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1617104678098-de229db51175?w=600",
                "https://images.unsplash.com/photo-1615873968403-89e068629265?w=600",
                "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600"
            ],
            features: [
                {icon: "💫", title: "Statement Lighting", desc: "Custom crystal chandelier"},
                {icon: "🍽️", title: "Custom Table", desc: "Handcrafted dining table for 12"},
                {icon: "🎭", title: "Accent Wall", desc: "Textured feature wall design"},
                {icon: "🪟", title: "Garden Views", desc: "Optimized outdoor connection"}
            ]
        },
        {
            id: 5,
            title: "Modern Kitchen",
            category: "Residential • Culinary Space",
            client: "The Rodriguez Family",
            location: "Austin, TX",
            year: "2024",
            size: "900 sq ft",
            description: "A contemporary kitchen that combines cutting-edge functionality with stunning aesthetics. This culinary workspace features high-end appliances seamlessly integrated into custom cabinetry. We designed multiple work zones to accommodate both everyday cooking and entertaining, with a large island serving as the heart of the space. Premium materials like quartz and stainless steel create a clean, professional aesthetic that's built to last.",
            heroImg: "https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600",
                "https://images.unsplash.com/photo-1565538810643-b5bdb714032a?w=600",
                "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600"
            ],
            features: [
                {icon: "🔪", title: "Professional Appliances", desc: "Commercial-grade kitchen equipment"},
                {icon: "🏝️", title: "Large Island", desc: "Multi-functional centerpiece"},
                {icon: "💧", title: "Smart Faucets", desc: "Touchless technology"},
                {icon: "📦", title: "Custom Storage", desc: "Maximized organization solutions"}
            ]
        },
        {
            id: 6,
            title: "Spa-Like Bathroom",
            category: "Residential • Wellness Space",
            client: "The Williams Family",
            location: "Seattle, WA",
            year: "2023",
            size: "450 sq ft",
            description: "A luxurious bathroom retreat inspired by high-end resort spas. This sanctuary features premium fixtures, natural stone, and a sophisticated color palette that promotes relaxation. We incorporated a freestanding soaking tub, spacious walk-in shower with multiple shower heads, and heated floors for ultimate comfort. Ambient lighting, custom vanities, and thoughtful storage solutions complete this personal wellness haven.",
            heroImg: "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800",
            gallery: [
                "https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=600",
                "https://images.unsplash.com/photo-1620626011761-996317b8d101?w=600",
                "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600"
            ],
            features: [
                {icon: "🛁", title: "Soaking Tub", desc: "Freestanding sculptural centerpiece"},
                {icon: "🚿", title: "Rain Shower", desc: "Multi-head spa shower system"},
                {icon: "🌡️", title: "Heated Floors", desc: "Radiant floor heating system"},
                {icon: "🪞", title: "LED Mirrors", desc: "Backlit vanity mirrors"}
            ]
        }
    ];

    // Open project details
    function openProject(projectId) {
        const project = projects.find(p => p.id === projectId);
        if (!project) return;

        document.getElementById('modalTitle').textContent = project.title;
        document.getElementById('modalCategory').textContent = project.category;
        document.getElementById('modalClient').textContent = project.client;
        document.getElementById('modalLocation').textContent = project.location;
        document.getElementById('modalYear').textContent = project.year;
        document.getElementById('modalSize').textContent = project.size;
        document.getElementById('modalDescription').textContent = project.description;
        document.getElementById('modalHeroImg').src = project.heroImg;

        // Add features
        const featuresContainer = document.getElementById('modalFeatures');
        featuresContainer.innerHTML = '';
        project.features.forEach(feature => {
            featuresContainer.innerHTML += `
                    <div class="feature-item">
                        <div class="feature-icon">${feature.icon}</div>
                        <div class="feature-text">
                            <h5>${feature.title}</h5>
                            <p>${feature.desc}</p>
                        </div>
                    </div>
                `;
        });

        // Add gallery images
        const galleryContainer = document.getElementById('modalGallery');
        galleryContainer.innerHTML = '';
        project.gallery.forEach(img => {
            galleryContainer.innerHTML += `<img src="${img}" alt="Gallery Image" class="gallery-img">`;
        });

        document.getElementById('projectModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Close project details
    function closeProject() {
        document.getElementById('projectModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('projectModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeProject();
        }
    });

    // Add click handlers to portfolio items
    document.addEventListener('DOMContentLoaded', function () {
        const portfolioItems = document.querySelectorAll('.portfolio-item');
        portfolioItems.forEach((item, index) => {
            item.addEventListener('click', () => openProject(index + 1));
        });
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    });

    // Header background on scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
        }
    });

    // Form submission
    document.querySelector('.contact-form').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Thank you for your message! We will get back to you soon.');
        e.target.reset();
    });
</script>
</body>
</html>