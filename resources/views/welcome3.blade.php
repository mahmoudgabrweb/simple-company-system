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

        header {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .hero {
            height: 100vh;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

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
            transition: all 0.3s;
        }

        .service-card:hover {
            transform: translateY(-10px);
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
            transition: opacity 0.3s;
            z-index: 1;
        }

        .portfolio-item:hover::before {
            opacity: 1;
        }

        .portfolio-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            transition: transform 0.3s;
        }

        .portfolio-item:hover .portfolio-overlay {
            transform: translateY(0);
        }

        .project-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 3000;
            overflow-y: auto;
        }

        .project-modal.show {
            display: block;
        }

        .modal-content {
            max-width: 1200px;
            margin: 4rem auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
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
            z-index: 3001;
        }

        .project-hero-img {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .project-details {
            padding: 3rem;
        }

        .project-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
        }

        .project-features {
            margin: 3rem 0;
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
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
        }

        .about {
            padding: 6rem 5%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }

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
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        footer {
            background: #1f2937;
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <div class="logo">LUXE INTERIORS</div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
</header>

<section id="home" class="hero">
    <div class="hero-content">
        <h1>Elevate Your Space</h1>
        <p>We create stunning interiors that blend luxury, functionality, and timeless elegance</p>
        <a href="#contact" class="cta-button">Start Your Project</a>
    </div>
</section>

<section id="services" class="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Comprehensive design solutions tailored to your unique style</p>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🏠</div>
                <h3>Residential Design</h3>
                <p>Transform your home into a personalized sanctuary with our expert residential design services.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🏢</div>
                <h3>Commercial Spaces</h3>
                <p>Elevate your business environment with sophisticated commercial interiors.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">✨</div>
                <h3>Luxury Renovations</h3>
                <p>Breathe new life into existing spaces with our comprehensive renovation services.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎨</div>
                <h3>Custom Furniture</h3>
                <p>Discover bespoke furniture pieces designed specifically for your space.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">💡</div>
                <h3>Lighting Design</h3>
                <p>Master the art of ambiance with our innovative lighting solutions.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🪴</div>
                <h3>Space Planning</h3>
                <p>Optimize your layout with strategic space planning.</p>
            </div>
        </div>
    </div>
</section>

<section id="portfolio" class="portfolio">
    <div class="container">
        <h2 class="section-title">Our Work</h2>
        <p class="section-subtitle">Click on any project to see details</p>
        <div class="portfolio-grid">
            <div class="portfolio-item" onclick="openProject(1)">
                <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Living Room</h3>
                    <p>Click to view details</p>
                </div>
            </div>
            <div class="portfolio-item" onclick="openProject(2)">
                <img src="https://images.unsplash.com/photo-1556912173-46c336c7fd55?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Luxury Bedroom</h3>
                    <p>Click to view details</p>
                </div>
            </div>
            <div class="portfolio-item" onclick="openProject(3)">
                <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Executive Office</h3>
                    <p>Click to view details</p>
                </div>
            </div>
            <div class="portfolio-item" onclick="openProject(4)">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Elegant Dining</h3>
                    <p>Click to view details</p>
                </div>
            </div>
            <div class="portfolio-item" onclick="openProject(5)">
                <img src="https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Kitchen</h3>
                    <p>Click to view details</p>
                </div>
            </div>
            <div class="portfolio-item" onclick="openProject(6)">
                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800" class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Spa Bathroom</h3>
                    <p>Click to view details</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about">
    <h2>About Our Studio</h2>
    <p>With over 15 years of experience in luxury interior design, we transform spaces into stunning environments.</p>
</section>

<section id="contact" class="contact">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <form class="contact-form">
            <div class="form-group">
                <input type="text" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <input type="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <textarea placeholder="Message" rows="5" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <p>&copy; 2025 Luxe Interiors. All rights reserved.</p>
</footer>

<div id="modal" class="project-modal">
    <div class="close-modal" onclick="closeProject()">×</div>
    <div class="modal-content">
        <img id="heroImg" class="project-hero-img">
        <div class="project-details">
            <h2 id="title" class="project-title"></h2>
            <p id="category" style="color: #666; margin-bottom: 2rem;"></p>
            <div class="project-info">
                <div class="info-item">
                    <h4>Client</h4>
                    <p id="client"></p>
                </div>
                <div class="info-item">
                    <h4>Location</h4>
                    <p id="location"></p>
                </div>
                <div class="info-item">
                    <h4>Year</h4>
                    <p id="year"></p>
                </div>
                <div class="info-item">
                    <h4>Size</h4>
                    <p id="size"></p>
                </div>
            </div>
            <p id="desc" style="line-height: 1.8; margin: 2rem 0;"></p>
            <div class="project-features">
                <h3>Key Features</h3>
                <div id="features" class="features-list"></div>
            </div>
            <div id="gallery" class="project-gallery"></div>
        </div>
    </div>
</div>

<script>
    const data = {
        1: {
            title: "Modern Living Room",
            category: "Residential • Living Space",
            client: "Anderson Family",
            location: "Beverly Hills, CA",
            year: "2024",
            size: "1,200 sq ft",
            desc: "A contemporary transformation creating a modern sanctuary with natural materials and custom lighting.",
            hero: "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800",
            gallery: ["https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?w=600", "https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=600", "https://images.unsplash.com/photo-1617806118233-18e1de247200?w=600"],
            features: [{i: "💡", t: "Smart Lighting", d: "Automated system"}, {
                i: "🪟",
                t: "Large Windows",
                d: "Natural light"
            }, {i: "🎨", t: "Art Wall", d: "Custom gallery"}, {i: "🛋️", t: "Italian Furniture", d: "Luxury pieces"}]
        },
        2: {
            title: "Luxury Bedroom Suite",
            category: "Residential • Private Quarters",
            client: "Confidential",
            location: "Manhattan, NY",
            year: "2024",
            size: "850 sq ft",
            desc: "An opulent master bedroom retreat designed for ultimate comfort with layered textures and ambient lighting.",
            hero: "https://images.unsplash.com/photo-1556912173-46c336c7fd55?w=800",
            gallery: ["https://images.unsplash.com/photo-1540518614846-7eded433c457?w=600", "https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=600", "https://images.unsplash.com/photo-1631889993959-41b4e9c6e3c5?w=600"],
            features: [{i: "🌙", t: "Blackout System", d: "Automated treatments"}, {
                i: "🔇",
                t: "Acoustic Panels",
                d: "Sound dampening"
            }, {i: "🛏️", t: "Custom Headboard", d: "Hand-crafted"}, {
                i: "💎",
                t: "Luxury Materials",
                d: "Premium fabrics"
            }]
        },
        3: {
            title: "Executive Office",
            category: "Commercial • Workspace",
            client: "Tech Innovations Inc.",
            location: "San Francisco, CA",
            year: "2024",
            size: "2,000 sq ft",
            desc: "A sophisticated executive office balancing professionalism with modern design and state-of-the-art technology.",
            hero: "https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800",
            gallery: ["https://images.unsplash.com/photo-1497366216548-37526070297c?w=600", "https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=600", "https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600"],
            features: [{i: "📱", t: "Smart Tech", d: "Integrated systems"}, {
                i: "📚",
                t: "Library Wall",
                d: "Custom bookshelf"
            }, {i: "🪑", t: "Ergonomic", d: "Premium furniture"}, {
                i: "🖼️",
                t: "Art Collection",
                d: "Contemporary pieces"
            }]
        },
        4: {
            title: "Elegant Dining Space",
            category: "Residential • Dining Room",
            client: "Chen Family",
            location: "Miami, FL",
            year: "2023",
            size: "600 sq ft",
            desc: "A refined dining room celebrating the art of gathering with understated luxury and statement chandelier.",
            hero: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800",
            gallery: ["https://images.unsplash.com/photo-1617104678098-de229db51175?w=600", "https://images.unsplash.com/photo-1615873968403-89e068629265?w=600", "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600"],
            features: [{i: "💫", t: "Statement Lighting", d: "Crystal chandelier"}, {
                i: "🍽️",
                t: "Custom Table",
                d: "Seats 12"
            }, {i: "🎭", t: "Accent Wall", d: "Textured design"}, {i: "🪟", t: "Garden Views", d: "Outdoor connection"}]
        },
        5: {
            title: "Modern Kitchen",
            category: "Residential • Culinary Space",
            client: "Rodriguez Family",
            location: "Austin, TX",
            year: "2024",
            size: "900 sq ft",
            desc: "A contemporary kitchen combining cutting-edge functionality with stunning aesthetics and professional appliances.",
            hero: "https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=800",
            gallery: ["https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600", "https://images.unsplash.com/photo-1565538810643-b5bdb714032a?w=600", "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600"],
            features: [{i: "🔪", t: "Pro Appliances", d: "Commercial grade"}, {
                i: "🏝️",
                t: "Large Island",
                d: "Multi-functional"
            }, {i: "💧", t: "Smart Faucets", d: "Touchless tech"}, {i: "📦", t: "Custom Storage", d: "Maximized space"}]
        },
        6: {
            title: "Spa-Like Bathroom",
            category: "Residential • Wellness Space",
            client: "Williams Family",
            location: "Seattle, WA",
            year: "2023",
            size: "450 sq ft",
            desc: "A luxurious bathroom retreat inspired by high-end resort spas with freestanding tub and heated floors.",
            hero: "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800",
            gallery: ["https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=600", "https://images.unsplash.com/photo-1620626011761-996317b8d101?w=600", "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600"],
            features: [{i: "🛁", t: "Soaking Tub", d: "Freestanding"}, {
                i: "🚿",
                t: "Rain Shower",
                d: "Multi-head spa"
            }, {i: "🌡️", t: "Heated Floors", d: "Radiant heating"}, {i: "🪞", t: "LED Mirrors", d: "Backlit vanity"}]
        }
    };

    function openProject(id) {
        const p = data[id];
        document.getElementById('heroImg').src = p.hero;
        document.getElementById('title').textContent = p.title;
        document.getElementById('category').textContent = p.category;
        document.getElementById('client').textContent = p.client;
        document.getElementById('location').textContent = p.location;
        document.getElementById('year').textContent = p.year;
        document.getElementById('size').textContent = p.size;
        document.getElementById('desc').textContent = p.desc;

        let fHtml = '';
        p.features.forEach(f => {
            fHtml += `<div class="feature-item"><div class="feature-icon">${f.i}</div><div><h5>${f.t}</h5><p>${f.d}</p></div></div>`;
        });
        document.getElementById('features').innerHTML = fHtml;

        let gHtml = '';
        p.gallery.forEach(img => {
            gHtml += `<img src="${img}" class="gallery-img">`;
        });
        document.getElementById('gallery').innerHTML = gHtml;

        document.getElementById('modal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeProject() {
        document.getElementById('modal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    document.getElementById('modal').onclick = function (e) {
        if (e.target === this) closeProject();
    };
</script>
</body>
</html>