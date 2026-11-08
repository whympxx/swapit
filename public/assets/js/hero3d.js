/* SWAP-IT — adegan 3D di hero (Three.js r128)
 * Inti "torus knot" bercahaya, kartu foto barang yang mengorbit dan saling bertukar
 * posisi, bentuk-bentuk geometris melayang, dan partikel. Bereaksi terhadap kursor,
 * bisa diputar dengan drag, dan berhenti merender saat tidak terlihat.
 */
(function () {
    'use strict';

    var host = document.getElementById('hero3d');
    if (!host) return;
    var visual = host.closest('.hero-visual') || host.parentNode;

    function fallback() { visual.classList.add('no-webgl'); }
    if (!window.THREE) return fallback();

    var renderer;
    try {
        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
        if (!renderer.getContext()) throw new Error('no context');
    } catch (e) {
        return fallback();
    }

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isSmall = window.innerWidth < 640;

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.setClearColor(0x000000, 0);
    host.appendChild(renderer.domElement);

    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    camera.position.set(0, 0, isSmall ? 13 : 11.5);

    // ===== Cahaya =====
    scene.add(new THREE.AmbientLight(0xffffff, 0.5));
    var key = new THREE.DirectionalLight(0xffffff, 0.9);
    key.position.set(4, 6, 8);
    scene.add(key);
    var lightViolet = new THREE.PointLight(0x8b5cf6, 2.4, 30);
    lightViolet.position.set(-6, 2, 4);
    scene.add(lightViolet);
    var lightPink = new THREE.PointLight(0xff4ecd, 1.8, 30);
    lightPink.position.set(6, -3, 3);
    scene.add(lightPink);
    var lightLime = new THREE.PointLight(0xc8ff3d, 1.5, 25);
    lightLime.position.set(0, 5, -4);
    scene.add(lightLime);

    var world = new THREE.Group();
    scene.add(world);

    // ===== Inti =====
    var core = new THREE.Group();
    world.add(core);
    var knot = new THREE.Mesh(
        new THREE.TorusKnotGeometry(0.85, 0.27, 200, 28, 2, 3),
        new THREE.MeshStandardMaterial({ color: 0xc8ff3d, metalness: 0.4, roughness: 0.18, emissive: 0x3a5200, emissiveIntensity: 0.35 })
    );
    core.add(knot);
    var shellMat = new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true, transparent: true, opacity: 0.12 });
    var shell = new THREE.Mesh(new THREE.IcosahedronGeometry(1.8, 1), shellMat);
    core.add(shell);

    // Cincin orbit
    var ringMat = new THREE.MeshBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.55 });
    var ring = new THREE.Mesh(new THREE.TorusGeometry(3.4, 0.012, 8, 200), ringMat);
    ring.rotation.x = Math.PI / 2;
    var orbit = new THREE.Group();
    orbit.rotation.x = 0.32;
    orbit.rotation.z = -0.12;
    orbit.add(ring);
    world.add(orbit);

    // ===== Kartu foto barang =====
    function roundedRect(w, h, r) {
        var s = new THREE.Shape();
        var x = -w / 2, y = -h / 2;
        s.moveTo(x + r, y);
        s.lineTo(x + w - r, y);
        s.quadraticCurveTo(x + w, y, x + w, y + r);
        s.lineTo(x + w, y + h - r);
        s.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        s.lineTo(x + r, y + h);
        s.quadraticCurveTo(x, y + h, x, y + h - r);
        s.lineTo(x, y + r);
        s.quadraticCurveTo(x, y, x + r, y);
        return s;
    }

    var photos = [];
    try { photos = JSON.parse(host.getAttribute('data-photos') || '[]'); } catch (e) { photos = []; }

    var CARD_W = 1.5, CARD_H = 1.85, DEPTH = 0.06;
    var cardCount = Math.max(3, Math.min(4, photos.length || 3));
    var palette = [0x8b5cf6, 0xff4ecd, 0x22d3ee, 0xff8a3d];
    var loader = new THREE.TextureLoader();
    var frameGeo = new THREE.ExtrudeGeometry(roundedRect(CARD_W + 0.14, CARD_H + 0.14, 0.2), {
        depth: DEPTH, bevelEnabled: true, bevelThickness: 0.02, bevelSize: 0.02, bevelSegments: 3, curveSegments: 10
    });
    frameGeo.translate(0, 0, -DEPTH / 2);
    var photoGeo = new THREE.PlaneGeometry(CARD_W, CARD_H);
    var cards = [];

    function coverTexture(tex) {
        var img = tex.image;
        if (!img || !img.width) return;
        var imgAspect = img.width / img.height;
        var planeAspect = CARD_W / CARD_H;
        if (imgAspect > planeAspect) {
            tex.repeat.set(planeAspect / imgAspect, 1);
            tex.offset.set((1 - tex.repeat.x) / 2, 0);
        } else {
            tex.repeat.set(1, imgAspect / planeAspect);
            tex.offset.set(0, (1 - tex.repeat.y) / 2);
        }
        tex.needsUpdate = true;
    }

    for (var i = 0; i < cardCount; i++) {
        var card = new THREE.Group();
        var frameMesh = new THREE.Mesh(frameGeo, new THREE.MeshStandardMaterial({
            color: 0x1a1a28, metalness: 0.5, roughness: 0.25, emissive: palette[i % palette.length], emissiveIntensity: 0.18
        }));
        card.add(frameMesh);

        var photoMat = new THREE.MeshBasicMaterial({ color: palette[i % palette.length] });
        var front = new THREE.Mesh(photoGeo, photoMat);
        front.position.z = DEPTH / 2 + 0.025;
        card.add(front);
        var back = new THREE.Mesh(photoGeo, photoMat);
        back.position.z = -DEPTH / 2 - 0.025;
        back.rotation.y = Math.PI;
        card.add(back);

        if (photos[i]) {
            (function (mat, url) {
                loader.load(url, function (tex) {
                    tex.encoding = THREE.sRGBEncoding;
                    tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
                    coverTexture(tex);
                    mat.map = tex;
                    mat.color.set(0xffffff);
                    mat.needsUpdate = true;
                });
            })(photoMat, photos[i]);
        }

        card.userData = { angle: (i / cardCount) * Math.PI * 2, bob: Math.random() * Math.PI * 2 };
        orbit.add(card);
        cards.push(card);
    }

    // ===== Bentuk melayang =====
    var shapeGeos = [
        new THREE.OctahedronGeometry(0.42),
        new THREE.BoxGeometry(0.55, 0.55, 0.55),
        new THREE.TorusGeometry(0.32, 0.13, 16, 40),
        new THREE.IcosahedronGeometry(0.4),
        new THREE.ConeGeometry(0.34, 0.65, 24),
        new THREE.SphereGeometry(0.3, 32, 32),
        new THREE.DodecahedronGeometry(0.38)
    ];
    var shapeColors = [0x8b5cf6, 0xff4ecd, 0x22d3ee, 0xff8a3d, 0xc8ff3d, 0xffffff];
    var floaters = [];
    var floaterCount = isSmall ? 8 : 14;
    for (var f = 0; f < floaterCount; f++) {
        var mesh = new THREE.Mesh(
            shapeGeos[f % shapeGeos.length],
            new THREE.MeshStandardMaterial({
                color: shapeColors[f % shapeColors.length],
                metalness: 0.3,
                roughness: 0.3,
                flatShading: f % 3 === 0
            })
        );
        var theta = Math.random() * Math.PI * 2;
        var phi = Math.acos(2 * Math.random() - 1);
        var r = 4.6 + Math.random() * 1.8;
        mesh.position.set(r * Math.sin(phi) * Math.cos(theta), r * Math.cos(phi) * 0.7, r * Math.sin(phi) * Math.sin(theta) * 0.6 - 1);
        var sc = 0.6 + Math.random() * 0.7;
        mesh.scale.setScalar(sc);
        mesh.userData = {
            base: mesh.position.clone(),
            spin: new THREE.Vector3(Math.random() - 0.5, Math.random() - 0.5, Math.random() - 0.5).multiplyScalar(0.02),
            phase: Math.random() * Math.PI * 2
        };
        world.add(mesh);
        floaters.push(mesh);
    }

    // ===== Partikel =====
    var pCount = isSmall ? 250 : 500;
    var pPos = new Float32Array(pCount * 3);
    for (var p = 0; p < pCount; p++) {
        var pr = 5 + Math.random() * 6;
        var pt = Math.random() * Math.PI * 2;
        var pp = Math.acos(2 * Math.random() - 1);
        pPos[p * 3] = pr * Math.sin(pp) * Math.cos(pt);
        pPos[p * 3 + 1] = pr * Math.cos(pp);
        pPos[p * 3 + 2] = pr * Math.sin(pp) * Math.sin(pt) - 2;
    }
    var pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
    var pMat = new THREE.PointsMaterial({ color: 0xffffff, size: 0.035, transparent: true, opacity: 0.7, sizeAttenuation: true });
    var particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // ===== Tema terang / gelap =====
    function applyTheme() {
        var light = document.documentElement.getAttribute('data-theme') === 'light';
        shellMat.color.set(light ? 0x2a1a5e : 0xffffff);
        shellMat.opacity = light ? 0.18 : 0.12;
        pMat.color.set(light ? 0x5b3fc4 : 0xffffff);
        pMat.opacity = light ? 0.45 : 0.7;
        knot.material.color.set(light ? 0x9be01a : 0xc8ff3d);
    }
    applyTheme();
    document.addEventListener('themechange', applyTheme);

    // ===== Interaksi =====
    var pointer = { x: 0, y: 0 };
    var spinVelocity = 0;
    var dragging = false, lastX = 0;

    window.addEventListener('pointermove', function (e) {
        pointer.x = (e.clientX / window.innerWidth) * 2 - 1;
        pointer.y = (e.clientY / window.innerHeight) * 2 - 1;
        if (dragging) {
            spinVelocity += (e.clientX - lastX) * 0.0009;
            lastX = e.clientX;
        }
    }, { passive: true });
    host.addEventListener('pointerdown', function (e) { dragging = true; lastX = e.clientX; host.style.cursor = 'grabbing'; });
    window.addEventListener('pointerup', function () { dragging = false; host.style.cursor = 'grab'; });
    host.style.cursor = 'grab';
    host.style.touchAction = 'pan-y';

    // ===== Ukuran =====
    function resize() {
        var w = host.clientWidth, h = host.clientHeight;
        if (!w || !h) return;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    }
    resize();
    if ('ResizeObserver' in window) new ResizeObserver(resize).observe(host);
    else window.addEventListener('resize', resize);

    // ===== Loop =====
    var visible = true;
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) { visible = entries[0].isIntersecting; }).observe(host);
    }

    var clock = new THREE.Clock();
    var orbitAngle = 0;
    var intro = 0;
    var camTarget = new THREE.Vector3();

    function easeOutBack(t) { var c1 = 1.70158, c3 = c1 + 1; return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2); }

    function frame() {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) { clock.getDelta(); return; }

        var dt = Math.min(clock.getDelta(), 0.05);
        var t = clock.elapsedTime;
        var motion = reduceMotion ? 0 : 1;

        intro = Math.min(1, intro + dt * 0.8);
        world.scale.setScalar(0.4 + 0.6 * easeOutBack(intro));

        // Kursor memiringkan seluruh adegan
        world.rotation.y += ((pointer.x * 0.45) - world.rotation.y) * 0.05;
        world.rotation.x += ((pointer.y * 0.25) - world.rotation.x) * 0.05;

        knot.rotation.x += 0.006 * motion;
        knot.rotation.y += 0.01 * motion;
        shell.rotation.y -= 0.003 * motion;
        shell.rotation.z += 0.002 * motion;
        core.position.y = Math.sin(t * 1.2) * 0.12 * motion;

        // Orbit kartu: otomatis + dorongan drag
        spinVelocity *= 0.95;
        orbitAngle += (0.0045 * motion + spinVelocity);
        for (var c = 0; c < cards.length; c++) {
            var cd = cards[c];
            var a = cd.userData.angle + orbitAngle;
            cd.position.set(Math.cos(a) * 3.4, Math.sin(t * 1.4 + cd.userData.bob) * 0.18 * motion, Math.sin(a) * 3.4);
            cd.updateMatrixWorld();
            camTarget.copy(camera.position);
            cd.lookAt(camTarget);
            cd.rotateZ(Math.sin(t + cd.userData.bob) * 0.06 * motion);
        }

        for (var k = 0; k < floaters.length; k++) {
            var m = floaters[k];
            m.rotation.x += m.userData.spin.x * motion;
            m.rotation.y += m.userData.spin.y * motion;
            m.rotation.z += m.userData.spin.z * motion;
            m.position.y = m.userData.base.y + Math.sin(t * 0.9 + m.userData.phase) * 0.25 * motion;
        }

        particles.rotation.y = t * 0.02 * motion;
        lightViolet.position.x = Math.sin(t * 0.6) * 6;
        lightPink.position.y = Math.cos(t * 0.5) * 4;

        renderer.render(scene, camera);
    }
    frame();
})();
