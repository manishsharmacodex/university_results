class Slider {
  constructor({
    containerSelector = ".slides",
    slideSelector = ".slide",
    nextBtnSelector = ".next",
    prevBtnSelector = ".prev",
    intervalTime = 10000,
  } = {}) {
    // Elements
    this.container = document.querySelector(containerSelector);
    this.slides = document.querySelectorAll(slideSelector);
    this.nextBtn = document.querySelector(nextBtnSelector);
    this.prevBtn = document.querySelector(prevBtnSelector);

    // State
    this.index = 0;
    this.intervalTime = intervalTime;
    this.interval = null;

    // Swipe
    this.startX = 0;
    this.endX = 0;

    if (!this.slides.length || !this.container) return;

    this.init();
  }

  init() {
    this.showSlide(this.index);
    this.startAuto();

    this.bindEvents();
  }

  /* -------------------------
           Core Functions
        --------------------------*/

  showSlide(i) {
    const total = this.slides.length;

    this.slides.forEach((slide) => slide.classList.remove("active"));

    this.index = (i + total) % total;
    this.slides[this.index].classList.add("active");
  }

  nextSlide = () => {
    this.showSlide(this.index + 1);
  };

  prevSlide = () => {
    this.showSlide(this.index - 1);
  };

  /* -------------------------
           Auto Slide (safe)
        --------------------------*/

  startAuto() {
    this.stopAuto(); // prevent multiple intervals
    this.interval = setInterval(this.nextSlide, this.intervalTime);
  }

  stopAuto() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }

  /* -------------------------
           Events
        --------------------------*/

  bindEvents() {
    // Buttons
    this.nextBtn?.addEventListener("click", () => {
      this.nextSlide();
      this.restartAuto();
    });

    this.prevBtn?.addEventListener("click", () => {
      this.prevSlide();
      this.restartAuto();
    });

    // Pause on hover
    this.container.addEventListener("mouseenter", () => this.stopAuto());
    this.container.addEventListener("mouseleave", () => this.startAuto());

    // Keyboard support
    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowRight") {
        this.nextSlide();
        this.restartAuto();
      }
      if (e.key === "ArrowLeft") {
        this.prevSlide();
        this.restartAuto();
      }
    });

    // Touch support (mobile swipe)
    this.container.addEventListener("touchstart", (e) => {
      this.startX = e.touches[0].clientX;
    });

    this.container.addEventListener("touchend", (e) => {
      this.endX = e.changedTouches[0].clientX;
      this.handleSwipe();
    });
  }

  handleSwipe() {
    const diff = this.startX - this.endX;

    if (Math.abs(diff) > 50) {
      if (diff > 0) this.nextSlide();
      else this.prevSlide();

      this.restartAuto();
    }
  }

  restartAuto() {
    this.stopAuto();
    this.startAuto();
  }
}

/* -------------------------
       INIT SLIDER
    --------------------------*/

document.addEventListener("DOMContentLoaded", () => {
  new Slider({
    intervalTime: 10000,
  });
});
