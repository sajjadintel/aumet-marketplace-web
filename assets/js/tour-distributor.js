class Tour {
    constructor(name) {
        this.name = name;
        this.steps = [];
        this.box;

        this.titleBackground;
        this.background;
        this.accentColor;
        this.borderRadius;

        this.scroll = true;

    }

    style(info = {}) {
        if (info.titleBackground != undefined) {
            this.titleBackground = info.titleBackground;
        }
        if (info.background != undefined) {
            this.background = info.background;
        }
        if (info.accentColor != undefined) {
            this.accentColor = info.accentColor;
        }
        if (info.borderRadius != undefined) {
            this.borderRadius = info.borderRadius;
        }
    }

    addStep(name, info = {}) {
        var step = new Step(name, info.title, info.text, info.hook, info.timer, info.onShow, info.onHide, info.buttons, info.links);
        this.steps.push(step);
    }

    start() {
        var length = this.steps.length;
        this.show(0);
    }

    next() {
        this.remove();
        this.show(this.currentStep + 1);
    }

    previous() {
        this.remove();
        this.show(this.currentStep - 1);
    }

    stop() {
        this.remove();
        this.currentStep = 0;
    }

    pause() {
        this.remove();
    }

    resume() {
        this.show(this.currentStep);
    }

    remove() {
        clearTimeout(this.timeout);
        // Animation
        var box = this.box;
        var self = this;
        $(this.box).animate({
            marginTop: "50px",
            opacity: "0"
        }, 300, function() {
            box.remove();
        });

        if (this.steps[this.currentStep].onHide != undefined) {
            this.steps[this.currentStep].onHide();
        }
    }

    setScroll(value) {
        this.scroll = value;
    }

    getScroll() {
        return this.scroll;
    }

    show(stepIndex) {

        this.currentStep = stepIndex;
        var hook = this.steps[stepIndex].hook;

        if (this.steps[stepIndex].onShow != undefined) {
            this.steps[stepIndex].onShow();
        };

        $.fn.isInViewport = function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();

            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            return elementBottom > viewportTop && elementTop < viewportBottom;
        };

        if (!$(hook).isInViewport() && this.getScroll()) {
            $('html,body').animate({
                scrollTop: $(this.steps[stepIndex].hook).offset().top
            },'slow');
        };

        this.box = $("<div class='tour'></div>");
        this.box.attr("id", this.steps[stepIndex].name);
        var titleContainer = $("<div class='titleContainer'></div>");
        var mainContainer = $("<div class='mainContainer'></div>");
        var actionContainer = $("<div class='actionContainer'></div>");
        var arrow = $("<div class='tour-arrow'></div>");

        $(hook).after(this.box);
        $(this.box).append(arrow);
        $(this.box).append(titleContainer);
        $(titleContainer).append("<h3>" + this.steps[stepIndex].title + "</h3>");
        $(this.box).append(mainContainer);
        $(mainContainer).append("<p>" + this.steps[stepIndex].text + "</p>");
        $(this.box).append(actionContainer);

        for (var i = 0; i < this.steps[stepIndex].links.length; i++) {
            $(actionContainer).append("<a href='" + this.steps[stepIndex].links[i].href + "'>" + this.steps[stepIndex].links[i].text + "</a>");
        }

        for (var i = 0; i < this.steps[stepIndex].buttons.length; i++) {
            $(actionContainer).append("<button onclick='" + this.steps[stepIndex].buttons[i].action + "'>" + this.steps[stepIndex].buttons[i].text + "</button>");
        }

        // Set custom styles
        (this.titleBackground != undefined) && document.body.style.setProperty('--TourTitleBackground', this.titleBackground);

        (this.background != undefined) && document.body.style.setProperty('--TourBackground', this.background);

        (this.accentColor != undefined) && document.body.style.setProperty('--TourAccent', this.accentColor);

        (this.borderRadius != undefined) && document.body.style.setProperty('--TourBorderRadius', this.borderRadius);

        // Animation
        $(this.box).animate({
            marginTop: "15px",
            opacity: "1"
        }, 300);

        var self = this;
        if (this.steps[stepIndex].timer != undefined) {
            this.timeout = setTimeout(function() {
                self.next();
            }, this.steps[stepIndex].timer);
        }
    }

}

class Step {
    constructor(name, title, text, hook, timer, onShow, onHide, buttons, links) {
        this.name = name;
        this.title = title;
        this.text = text;
        this.hook = hook;
        this.timer = timer;
        this.onShow = onShow;
        this.onHide = onHide;
        this.buttons = buttons;
        this.links = links;
    }
}



const WebGuidedTour = new Tour("demo");
WebGuidedTour.addStep('step1', {
    title: "Step 1",
    text: "Step 1",
    hook: "#kt_aside_toggle",
    timer: 5000,
    onShow: function() {
        // Custom Function
    },
    onHide: function() {
        // Custom Function
    },
    buttons: [
        {
            text: "Previous",
            action: "WebGuidedTour.previous()"
        },
        {
            text: "Next",
            action: "WebGuidedTour.next()"
        }
    ],
    links: [
        {
            text: "More info?",
            href: "#"
        }
    ]
});
WebGuidedTour.addStep('step2', {
    title: "Step 2",
    text: "Step 2",
    hook: "#kt_quick_user_toggle",
    timer: 5000,
    onShow: function() {
        // Custom Function
    },
    onHide: function() {
        // Custom Function
    },
    buttons: [
        {
            text: "Previous",
            action: "WebGuidedTour.previous()"
        },
        {
            text: "Next",
            action: "WebGuidedTour.next()"
        }
    ],
    links: [
        {
            text: "More info?",
            href: "#"
        }
    ]
});
WebGuidedTour.addStep('step3', {
    title: "Step 3",
    text: "Step 3",
    hook: "#pageContent",
    timer: 5000,
    onHide: function() {
        // Custom Function
    },
    buttons: [
        {
            text: "Previous",
            action: "WebGuidedTour.previous()"
        },
        {
            text: "Finish",
            action: "WebGuidedTour.stop()"
        }
    ],
    links: [
        {
            text: "More info?",
            href: "#"
        }
    ]
});
