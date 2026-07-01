export default class popupToast {
    constructor(message, title = "", options = {}) {
        const defaults = {
            type: "success",
            duration: 3000,
            autoClose: true,
            enableProgressBar: false,
            position: "top-center",
            checkIcon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>',
            closeIcon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>',
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            newestOnTop: true,
        };
        this.options = jQuery.extend({}, defaults, options);
        this.message = message;
        this.title = title;

        if (this.options.preventDuplicates && this.isDuplicate()) return;
        
        this.createToast();
        this.show();

        if (this.options.autoClose) {
            this.setupAutoClose();
        }
    }

    setupAutoClose() {
        const closeToast = () => {
            this.jQuerytoast[this.options.hideMethod]("slow", () => {
                this.jQuerytoast.remove()
                this.adjustToastPositions(); // Adjust positions after removal
            });
            clearTimeout(timer1);
        };

        const timer1 = setTimeout(() => {
            this.jQuerytoast[this.options.hideMethod]("slow", () => {
                this.jQuerytoast.remove()
                this.adjustToastPositions(); // Adjust positions after removal
            });
        }, this.options.duration);


        this.jQueryiconClose.on("click", closeToast);
    }

    isDuplicate() {
        return (
            jQuery(".notification-toast").filter((index, element) => {
                return jQuery(element).find(".message .text-2").text() === this.message;
            }).length > 0
        );
    }

    getIcon(type) {

        const svgList = {
            success : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>`,
            info : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                    </svg>`,
            warning : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                        </svg>`,
            danger : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>
                    </svg>`
        }
        
        return svgList[`${type}`];
    }

    createToast() {
        // Create the HTML structure based on the provided structure
        this.jQuerytoast = jQuery("<div/>", {
            class: `popup-toast ${this.options.type} active`,
            style: "top: 60px;"
        });
    
        const popupContent = jQuery("<div/>", {
            class: "popup-content"
        });
    
        const imageContainer = jQuery("<div/>", {
            class: "image-container"
        });
    
        const imageIcon = jQuery("<div/>", {
            class: "image-icon"
        });
    
        const image = jQuery("<img/>", {
            src: "", // Add image source if available
            alt: "image",
            id: "image"
        });
    
        imageIcon.append(image);
    
        const message = jQuery("<div/>", {
            class: "message"
        });
    
        const messageTitle = jQuery("<h6/>", {
            class: "text text-1 mt-0 mb-2",
            text: this.title
        });
    
        message.append(messageTitle);
    
        const buttonContainer = jQuery("<div/>", {
            class: "button"
        });
    
        const yesButton = jQuery("<button/>", {
            type: "button",
            class: "btn bg-white text-uppercase yes-btn",
            id: "yes-btn",
            text: "Yes"
        });
    
        const noButton = jQuery("<button/>", {
            type: "button",
            class: "btn bg-white text-uppercase no-btn",
            id: "no-btn",
            text: "No"
        });
    
        buttonContainer.append(yesButton, noButton);
    
        imageContainer.append(imageIcon, message, buttonContainer);
        popupContent.append(imageContainer);
        this.jQuerytoast.append(popupContent);
    
        jQuery("body").append(this.jQuerytoast);
        this.adjustToastPositions();
    
        function addActive() {
            this.jQuerytoast.addClass('active');
        }
    
        setTimeout(() => addActive.call(this), 300);
    }
    
    adjustToastPositions() {
        const existingToasts = jQuery(".notification-toast").not(this.jQuerytoast);
        let verticalPosition = 60;

        existingToasts.each(function () {
            verticalPosition += jQuery(this).outerHeight(true);
        });

        if (this.options.position === "top-left" || this.options.position === "top-center" || this.options.position === "top-right") {
            this.jQuerytoast.css("top", verticalPosition);
        } else {
            this.jQuerytoast.css("bottom", verticalPosition);
        }
    }

    show() {
        this.jQuerytoast[this.options.showMethod]("slow");
    }

    static success(message, title = "", options = {}) {
        options.type = "success";
        new popupToast(message, title, options);
    }

    static info(message, title = "", options = {}) {
        options.type = "info";
        new popupToast(message, title, options);
    }

    static warning(message, title = "", options = {}) {
        options.type = "warning";
        new popupToast(message, title, options);
    }

    static error(message, title = "", options = {}) {
        options.type = "danger";
        new popupToast(message, title, options);
    }

    static remove() {
        jQuery(".notification-toast").remove();
    }

    static clear() {
        jQuery(".notification-toast").remove();
    }
}
