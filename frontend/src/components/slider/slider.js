class Slider {
  constructor({
    containerSelector = ".slides",
    slideSelector = ".slide",
    nextBtnSelector = ".next",
    prevBtnSelector = ".prev",
    intervalTime = 10000,
    swipeThreshold = 50,
  } = {}) {
    // Elements
    this.container = document.querySelector(containerSelector);
    this.slides = this.container
      ? this.container.querySelectorAll(slideSelector)
      : [];
    this.nextBtn = document.querySelector(nextBtnSelector);
    this.prevBtn = document.querySelector(prevBtnSelector);

    // Guard clause
    if (!this.container || !this.slides.length) return;

    // State
    this.index = 0;
    this.intervalTime = intervalTime;
    this.interval = null;
    this.swipeThreshold = swipeThreshold;

    // Touch
    this.startX = 0;
    this.endX = 0;

    this.init();
  }

  /* -------------------------
        INIT
  --------------------------*/
  init() {
    this.showSlide(this.index);
    this.startAuto();
    this.bindEvents();
  }

  /* -------------------------
        CORE
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
        AUTO PLAY
  --------------------------*/
  startAuto() {
    this.stopAuto();
    this.interval = setInterval(() => {
      this.nextSlide();
    }, this.intervalTime);
  }

  stopAuto() {
    clearInterval(this.interval);
    this.interval = null;
  }

  restartAuto() {
    this.startAuto();
  }

  /* -------------------------
        EVENTS
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

    // Keyboard
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

    // Touch
    this.container.addEventListener("touchstart", (e) => {
      this.startX = e.touches[0].clientX;
      this.stopAuto();
    });

    this.container.addEventListener("touchend", (e) => {
      this.endX = e.changedTouches[0].clientX;
      this.handleSwipe();
      this.startAuto();
    });
  }

  /* -------------------------
        SWIPE
  --------------------------*/
  handleSwipe() {
    const diff = this.startX - this.endX;

    if (Math.abs(diff) > this.swipeThreshold) {
      if (diff > 0) this.nextSlide();
      else this.prevSlide();

      this.restartAuto();
    }
  }
}

/* -------------------------
        INIT
--------------------------*/
document.addEventListener("DOMContentLoaded", () => {
  new Slider({
    intervalTime: 10000,
  });
});
