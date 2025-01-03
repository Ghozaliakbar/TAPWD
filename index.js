

//menu
var tombolMenu = $(".tombol-menu");
var menu = $("nav .menu ul");

function klikMenu() {
    tombolMenu.click(function () {
        menu.toggle();
    });
    menu.click(function () {
        menu.toggle();
    });
}

$(document).ready(function () {
    var width = $(window).width();
    if(width < 990) {
        klikMenu();
    }
})

//check lebar
$(window).resize(function () {
    var width = $(window).width();
    if(width > 989) {
        menu.css("display", "block");
        //display:block
    } else {
        menu.css("display", "none");
    }
    klikMenu();
});

//efek scroll
$(document).ready(function () {
    var scroll_pos = 0;
    $(document).scroll(function() {
        scroll_pos = $(this).scrollTop();
        if(scroll_pos > 0) {
            $("nav").addClass("putih");
            $("nav img.hitam").show();
            $("nav img.putih").hide();
        } else {
            $("nav").removeClass("putih");
            $("nav img.hitam").hide();
            $("nav img.putih").show();
        }
    })
})

// Show next set of slides
nextBtn.addEventListener('click', () => {
    if (currentIndex < slideItems.length - slidesToShow) {
        currentIndex++;
        updateSlidePosition();
    }
});

// Show previous set of slides
prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateSlidePosition();
    }
});

// Update slide position
function updateSlidePosition() {
    slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}

// Slider functionality
const slides = document.querySelector('.slides');
const slideItems = document.querySelectorAll('.slide');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;
const slidesToShow = 3; // Number of slides to display
const slideWidth = slideItems[0].clientWidth + 20; // Include gap

// Show next set of slides
nextBtn.addEventListener('click', () => {
    if (currentIndex < slideItems.length - slidesToShow) {
        currentIndex++;
        updateSlidePosition();
    }
});

// Show previous set of slides
prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateSlidePosition();
    }
});

// Update slide position
function updateSlidePosition() {
    slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}
