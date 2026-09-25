/* Pelham Services — shared front-end behavior (vanilla JS, no dependencies) */

(function () {
  "use strict";

  /* ---------------------------------------------------------------------
     Mobile navigation toggle
     ------------------------------------------------------------------- */
  function initNavToggle() {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.getElementById("main-nav");
    if (!toggle || !nav) return;

    toggle.addEventListener("click", function () {
      var isOpen = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(isOpen));
      document.body.style.overflow = isOpen ? "hidden" : "";
    });

    nav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        nav.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
      });
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && nav.classList.contains("is-open")) {
        nav.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
        toggle.focus();
      }
    });
  }

  /* ---------------------------------------------------------------------
     Active nav link (aria-current) based on current path
     ------------------------------------------------------------------- */
  function markActiveNav() {
    document.querySelectorAll(".main-nav__list a[aria-current]").forEach(function (link) {
      link.removeAttribute("aria-current");
    });
    document.querySelectorAll(".main-nav__list a").forEach(function (link) {
      var linkPath = new URL(link.href, location.href).pathname.replace(/index\.html$/, "");
      var currentPath = location.pathname.replace(/index\.html$/, "");
      if (linkPath === currentPath || (linkPath !== "/" && linkPath !== "" && currentPath.indexOf(linkPath) === 0)) {
        link.setAttribute("aria-current", "page");
      }
    });
  }

  /* ---------------------------------------------------------------------
     Scroll reveal via IntersectionObserver
     ------------------------------------------------------------------- */
  function initScrollReveal() {
    var targets = document.querySelectorAll("[data-reveal]");
    if (!targets.length) return;

    if (!("IntersectionObserver" in window)) {
      targets.forEach(function (t) { t.classList.add("is-visible"); });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
    );

    targets.forEach(function (t) { observer.observe(t); });
  }

  /* ---------------------------------------------------------------------
     Header shadow/solid state on scroll
     ------------------------------------------------------------------- */
  function initHeaderScrollState() {
    var header = document.querySelector(".site-header");
    if (!header) return;
    var onScroll = function () {
      header.classList.toggle("is-scrolled", window.scrollY > 8);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  /* ---------------------------------------------------------------------
     Client-side form validation
     Works for any <form data-validate> in the document.
     ------------------------------------------------------------------- */
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var PHONE_RE = /^[0-9()+\-.\s]{7,20}$/;

  function validateField(field) {
    var input = field.querySelector("input, select, textarea");
    if (!input) return true;

    var value = input.value.trim();
    var isRequired = input.hasAttribute("required");
    var errorEl = field.querySelector(".field-error");
    var valid = true;
    var message = "";

    if (isRequired && !value) {
      valid = false;
      message = "This field is required.";
    } else if (value && input.type === "email" && !EMAIL_RE.test(value)) {
      valid = false;
      message = "Enter a valid email address.";
    } else if (value && input.type === "tel" && !PHONE_RE.test(value)) {
      valid = false;
      message = "Enter a valid phone number.";
    } else if (input.type === "checkbox" && isRequired && !input.checked) {
      valid = false;
      message = "You must accept to continue.";
    }

    field.classList.toggle("has-error", !valid);
    if (errorEl && message) errorEl.textContent = message;
    return valid;
  }

  function initFormValidation() {
    var forms = document.querySelectorAll("form[data-validate]");
    forms.forEach(function (form) {
      var fields = form.querySelectorAll(".field");
      var status = form.querySelector(".form-status");

      fields.forEach(function (field) {
        var input = field.querySelector("input, select, textarea");
        if (!input) return;
        input.addEventListener("blur", function () { validateField(field); });
        input.addEventListener("input", function () {
          if (field.classList.contains("has-error")) validateField(field);
        });
      });

      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var allValid = true;
        fields.forEach(function (field) {
          if (!validateField(field)) allValid = false;
        });

        if (!status) return;

        if (!allValid) {
          status.className = "form-status is-error";
          status.textContent = "Please review the highlighted fields and try again.";
          status.setAttribute("role", "alert");
          var firstError = form.querySelector(".field.has-error input, .field.has-error select, .field.has-error textarea");
          if (firstError) firstError.focus();
          return;
        }

        /* No backend is wired up yet — this simulates a successful submit.
           Replace with a real fetch() to your form endpoint when ready. */
        status.className = "form-status is-success";
        status.textContent = form.dataset.successMessage ||
          "Thanks — your request has been received. Our team will follow up shortly.";
        status.setAttribute("role", "status");
        form.reset();
        fields.forEach(function (field) { field.classList.remove("has-error"); });
      });
    });
  }

  /* ---------------------------------------------------------------------
     Live character counter for textareas with data-maxlength
     ------------------------------------------------------------------- */
  function initCharCounters() {
    document.querySelectorAll("textarea[data-maxlength]").forEach(function (el) {
      var max = parseInt(el.getAttribute("data-maxlength"), 10);
      var counter = document.getElementById(el.getAttribute("aria-describedby"));
      if (!counter) return;
      var update = function () {
        var len = el.value.length;
        counter.textContent = len + " / " + max;
      };
      el.addEventListener("input", function () {
        if (el.value.length > max) el.value = el.value.slice(0, max);
        update();
      });
      update();
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    markActiveNav();
    initScrollReveal();
    initHeaderScrollState();
    initFormValidation();
    initCharCounters();
  });
})();
