const anim = gsap.to(item, {
  y: direction + columnHeight / 2,
  duration: LoopGallerySettings?.scrollSpeed || 10, // use localized scroll speed or fallback
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
