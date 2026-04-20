var nixsliderIndex = {};
var nixsliderTimers = {};

function initNixsliders() {
    var sliders = document.querySelectorAll(".nixslider-container");
    sliders.forEach(function(slider) {
        var id = slider.id.replace("nixslider-", "");
        nixsliderIndex[id] = 0;
        showNixslider(id, 0);
        
        var speed = parseInt(slider.getAttribute('data-speed')) || 5000;
        
        // Auto slide every 'speed' milliseconds
        nixsliderTimers[id] = setInterval(function() {
            moveNixslider(id, 1);
        }, speed);
        
        // Pause on hover
        slider.addEventListener('mouseenter', function() {
            clearInterval(nixsliderTimers[id]);
        });
        
        // Resume on mouse leave
        slider.addEventListener('mouseleave', function() {
            nixsliderTimers[id] = setInterval(function() {
                moveNixslider(id, 1);
            }, speed);
        });
        
        // Swipe support for touch devices
        let startX = 0;
        let endX = 0;
        
        slider.addEventListener('touchstart', function(e) {
            startX = e.changedTouches[0].screenX;
        }, {passive: true});
        
        slider.addEventListener('touchend', function(e) {
            endX = e.changedTouches[0].screenX;
            handleSwipe();
        }, {passive: true});
        
        function handleSwipe() {
            if (endX < startX - 50) {
                // swipe left (next)
                moveNixslider(id, 1);
            }
            if (endX > startX + 50) {
                // swipe right (prev)
                moveNixslider(id, -1);
            }
        }
    });
}

function moveNixslider(id, n) {
    showNixslider(id, nixsliderIndex[id] += n);
}

function currentNixslider(id, n) {
    showNixslider(id, nixsliderIndex[id] = n);
}

function showNixslider(id, n) {
    var i;
    var container = document.getElementById("nixslider-" + id);
    if (!container) return;
    
    var slides = container.getElementsByClassName("nixslider-slide");
    var dots = container.getElementsByClassName("nixslider-dot");
    
    if (slides.length === 0) return;
    
    if (n >= slides.length) {nixsliderIndex[id] = 0}
    if (n < 0) {nixsliderIndex[id] = slides.length - 1}
    
    for (i = 0; i < slides.length; i++) {
        slides[i].className = slides[i].className.replace(" active", "");
    }
    
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    slides[nixsliderIndex[id]].className += " active";
    if (dots.length > 0) dots[nixsliderIndex[id]].className += " active";
}

document.addEventListener("DOMContentLoaded", initNixsliders);
