class Slider {
  constructor({
    containerSelector = ".slides",
    nextBtnSelector = ".next",
    prevBtnSelector = ".prev",
    intervalTime = 10000,
    swipeThreshold = 60,
  } = {}) {
    // Elements
    this.container = document.querySelector(containerSelector);
    if (!this.container) return;

    this.slides = Array.from(this.container.querySelectorAll(".slide"));
    if (!this.slides.length) return;

    this.nextBtn = document.querySelector(nextBtnSelector);
    this.prevBtn = document.querySelector(prevBtnSelector);

    // State
    this.index = 0;
    this.intervalTime = intervalTime;
    this.interval = null;
    this.swipeThreshold = swipeThreshold;
    this.isAnimating = false;

    // Touch
    this.startX = 0;
    this.startY = 0;
    this.endX = 0;
    this.endY = 0;

    // Bind
    this.handleNext = this.nextSlide.bind(this);
    this.handlePrev = this.prevSlide.bind(this);
    this.handleKeydown = this.onKeydown.bind(this);
    this.handleTouchStart = this.onTouchStart.bind(this);
    this.handleTouchEnd = this.onTouchEnd.bind(this);
    this.handleMouseEnter = this.stopAuto.bind(this);
    this.handleMouseLeave = this.startAuto.bind(this);

    this.init();
  }

  /* INIT */
  init() {
    this.setA11y();
    this.showSlide(this.index, false);
    this.bindEvents();
    this.startAuto();
  }

  /* CORE SLIDER */
  showSlide(i, animate = true) {
    this.firstLoad = true;
    if (this.isAnimating) return;

    const total = this.slides.length;
    this.index = (i + total) % total;

    this.isAnimating = animate;

    // disable animation on first load
    if (this.firstLoad) {
      this.container.style.transition = "none";
    }

    this.container.style.transform = `translateX(-${this.index * 100}%)`;

    this.slides.forEach((slide, idx) => {
      slide.classList.toggle("active", idx === this.index);
      slide.setAttribute("aria-hidden", idx !== this.index);
    });

    // restore animation after first paint
    if (this.firstLoad) {
      requestAnimationFrame(() => {
        this.container.style.transition = "";
        this.firstLoad = false;
      });
    }

    if (animate) {
      setTimeout(() => {
        this.isAnimating = false;
      }, 600);
    }
  }

  nextSlide() {
    this.showSlide(this.index + 1);
  }

  prevSlide() {
    this.showSlide(this.index - 1);
  }

  /* AUTOPLAY */
  startAuto() {
    this.stopAuto();
    this.interval = setInterval(() => {
      this.nextSlide();
    }, this.intervalTime);
  }

  stopAuto() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }

  /* EVENTS */
  bindEvents() {
    this.nextBtn?.addEventListener("click", this.handleNext);
    this.prevBtn?.addEventListener("click", this.handlePrev);

    this.container.addEventListener("mouseenter", this.handleMouseEnter);
    this.container.addEventListener("mouseleave", this.handleMouseLeave);

    document.addEventListener("keydown", this.handleKeydown);

    this.container.addEventListener("touchstart", this.handleTouchStart, {
      passive: true,
    });

    this.container.addEventListener("touchend", this.handleTouchEnd);
  }

  onKeydown(e) {
    if (e.key === "ArrowRight") this.nextSlide();
    if (e.key === "ArrowLeft") this.prevSlide();
  }

  /* SWIPE */
  onTouchStart(e) {
    const t = e.touches[0];
    this.startX = t.clientX;
    this.startY = t.clientY;
    this.stopAuto();
  }

  onTouchEnd(e) {
    const t = e.changedTouches[0];
    this.endX = t.clientX;
    this.endY = t.clientY;

    const diffX = this.startX - this.endX;
    const diffY = this.startY - this.endY;

    if (Math.abs(diffY) > Math.abs(diffX)) return;

    if (Math.abs(diffX) > this.swipeThreshold) {
      diffX > 0 ? this.nextSlide() : this.prevSlide();
    }

    this.startAuto();
  }

  /* ACCESSIBILITY */
  setA11y() {
    this.container.setAttribute("role", "region");
    this.container.setAttribute("aria-label", "Image slider");

    this.slides.forEach((slide, i) => {
      slide.setAttribute("role", "group");
      slide.setAttribute("aria-roledescription", "slide");
      slide.setAttribute("aria-hidden", i !== 0);
    });
  }
}

/* INIT */
const slider = new Slider({
  intervalTime: 10000,
});
