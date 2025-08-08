console.clear();

gsap.registerPlugin(ScrollTrigger);

const additionalY = { val: 0 };
let additionalYAnim;
const cols = gsap.utils.toArray(".col");
let allAnimations = [];

// Duplicate images for seamless loop + set animations
cols.forEach((col, i) => {
  const images = Array.from(col.children);

  // Duplicate for infinite loop
  images.forEach(image => col.appendChild(image.cloneNode(true)));

  Array.from(col.children).forEach(item => {
    let columnHeight = item.parentElement.clientHeight;
    let direction = i % 2 !== 0 ? "+=" : "-=";
    let localOffset = 0;

    const anim = gsap.to(item, {
      y: direction + columnHeight / 2,
      duration: 10, // faster base movement
      repeat: -1,
      ease: "none",
      modifiers: {
        y: gsap.utils.unitize(y => {
          localOffset += additionalY.val;
          if (direction === "+=") {
            y = (parseFloat(y) - localOffset) % (columnHeight * 0.5);
          } else {
            y = (parseFloat(y) + localOffset) % -(columnHeight * 0.5);
          }
          return y;
        })
      }
    });

    allAnimations.push(anim);
  });
});

// Stronger scroll acceleration
ScrollTrigger.create({
  trigger: document.body,
  start: "top top",
  end: "bottom bottom",
  onUpdate: (self) => {
    const velocity = self.getVelocity();

    if (velocity !== 0) {
      if (additionalYAnim) additionalYAnim.kill();

      // MUCH bigger multiplier
      additionalY.val = -velocity / 300;

      // Shorter ease-back for snappier feel
      additionalYAnim = gsap.to(additionalY, { 
        val: 0, 
        duration: 0.6, 
        ease: "power3.out" 
      });
    }
  }
});

// Grab/pause logic
document.querySelectorAll(".col img").forEach(img => {
  img.style.cursor = "grab";

  img.addEventListener("mousedown", () => {
    img.style.cursor = "grabbing";
    pauseGallery();
  });
  img.addEventListener("touchstart", () => {
    img.style.cursor = "grabbing";
    pauseGallery();
  }, { passive: true });

  img.addEventListener("mouseup", () => {
    img.style.cursor = "grab";
    resumeGallery();
  });
  img.addEventListener("mouseleave", () => {
    img.style.cursor = "grab";
    resumeGallery();
  });
  img.addEventListener("touchend", () => {
    img.style.cursor = "grab";
    resumeGallery();
  });
});

function pauseGallery() {
  allAnimations.forEach(anim => anim.pause());
}

function resumeGallery() {
  allAnimations.forEach(anim => anim.resume());
}
