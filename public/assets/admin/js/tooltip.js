  const tooltips = document.querySelectorAll('.tooltip');

    Array.prototype.forEach.call(tooltips, function (el, i) {
      let tooltipButton = el.querySelector('.tooltip-button'),
      tooltipContent = el.querySelector('.tooltip-content'),
      /* Search for last focussable element inside tooltip (so that we can remove the tooltip after next tab) */
      tooltipContentItemsFocusable = tooltipContent.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'),
      tooltipContentItems = tooltipContentItemsFocusable[tooltipContentItemsFocusable.length - 1];

      /* set the tooltip position based on a top tooltip on screen */
      function setTooltipPosition () {
        /* if any positioning classes present -> remove them */
        positionClasses = ['top','right','bottom', 'left'];
        positionClasses.forEach(function(item) {
          tooltipContent.classList.remove(item);
        });


          /* Calculate tooltip space */

        const tooltipSpace = tooltipButton.getBoundingClientRect();
        const tooltipBox = tooltipContent.getBoundingClientRect();
        const tooltipRight = tooltipBox.right + tooltipBox.width;

          if (tooltipSpace.top > tooltipBox.height && tooltipBox.left > 0 && tooltipRight < window.innerWidth) {
              tooltipContent.classList.add('top')
          } else if (tooltipSpace.bottom > tooltipBox.height && tooltipBox.left > 0 && tooltipRight < window.innerWidth) {
              tooltipContent.classList.add('bottom')
          } else {
            if (tooltipBox.left > 0 && tooltipRight > window.innerWidth) {
                tooltipContent.classList.add('left')
            } else if (tooltipBox.left < 0 && tooltipRight < window.innerWidth) {
                tooltipContent.classList.add('right')
            } else {
              tooltipContent.classList.add('bottom')
            }
          }
      }

      setTooltipPosition();
      /* retrigger position on resize  */
      window.addEventListener("resize", () => {
        setTooltipPosition();
      });

      let mouseOverTooltip = false,
      mouseOverTooltipButton = false,
      focusOnTooltip = false;
      tooltipButton.addEventListener('click', function(element) {
        clicktooltipContent()
      });
      tooltipButton.addEventListener('mouseover', function(element) {
        mouseOverTooltipButton = true;
        showtooltipContent()
      });
      tooltipButton.addEventListener('mouseout', function(element) {
        mouseOverTooltipButton = false;
        /* Set small timeout for removing the tooltip to make user able to interract  */
        setTimeout(function(){
          if (!mouseOverTooltip) {
            hidetooltipContent()
          }
        }, 200);
      });
      tooltipButton.addEventListener('focus', function(element) {
        showtooltipContent()
      });
      tooltipButton.addEventListener('blur', function(element) {
        /* Set small timeout for removing the tooltip to make user able to interract  */
        setTimeout(function(){
          if (!focusOnTooltip) {
            hidetooltipContent()
          }
        }, 200);
      });

      /* escape key closes tooltip  */
      tooltipButton.addEventListener('keyup', function(element) {
        if(event.keyCode==27){hidetooltipContent();};
      });
      tooltipContent.addEventListener('keyup', function(element) {
        if(event.keyCode==27){hidetooltipContent();};
      });


      /* default mouse enters and leave  */
      tooltipContent.addEventListener('mouseenter', function(element) {
        mouseOverTooltip = true;
      });
      tooltipContent.addEventListener('mouseleave', function(element) {
        mouseOverTooltip = false;
        /* Set small timeout for removing the tooltip to make user able to interract  */
        setTimeout(function(){
          if (!mouseOverTooltipButton) {
            hidetooltipContent()
          }
        }, 200);

      });
      tooltipContent.addEventListener('focus', function(element) {
        focusOnTooltip = true;
        showtooltipContent()
      });
      if (tooltipContentItemsFocusable.length > 0) {
        tooltipContentItems.addEventListener('focus', function(element) {
          focusOnTooltip = true;
          showtooltipContent()
        });
        tooltipContentItems.addEventListener('blur', function(element) {
          focusOnTooltip = false;
          hidetooltipContent()
        });
      } else {
        tooltipContent.addEventListener('blur', function(element) {
          focusOnTooltip = false;
          hidetooltipContent()
        });
      }


      /* Functions for showing and hiding tooltip, add aria-expanded on button, not mandatory, but gives people with voice over an indicator something happened */
      function clicktooltipContent () {
        if(tooltipButton.getAttribute('aria-expanded')=='true'){tooltipContent.classList.remove('active'); tooltipButton.setAttribute('aria-expanded', 'false');} else { tooltipContent.classList.add('active'); tooltipButton.setAttribute('aria-expanded', 'true');};
      }
      function showtooltipContent () {
        tooltipContent.classList.add('active'); tooltipButton.setAttribute('aria-expanded', 'true');
      }
      function hidetooltipContent () {
        tooltipContent.classList.remove('active'); tooltipButton.setAttribute('aria-expanded', 'false');
      }
    });