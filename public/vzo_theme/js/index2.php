/*
     _ _      _       _
 ___| (_) ___| | __  (_)___
/ __| | |/ __| |/ /  | / __|
\__ \ | | (__|   < _ | \__ \
|___/_|_|\___|_|\_(_)/ |___/
                   |__/

 Version: 1.6.0
  Author: Ken Wheeler
 Website: http://kenwheeler.github.io
    Docs: http://kenwheeler.github.io/slick
    Repo: http://github.com/kenwheeler/slick
  Issues: http://github.com/kenwheeler/slick/issues

 */
/* global window, document, define, jQuery, setInterval, clearInterval */
(function(factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }

}(function($) {
    'use strict';
    var Slick = window.Slick || {};

    Slick = (function() {

        var instanceUid = 0;

        function Slick(element, settings) {

            var _ = this, dataSettings;

            _.defaults = {
                accessibility: true,
                adaptiveHeight: false,
                appendArrows: $(element),
                appendDots: $(element),
                arrows: true,
                asNavFor: null,
                prevArrow: '<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button">Previous</button>',
                nextArrow: '<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button">Next</button>',
                autoplay: false,
                autoplaySpeed: 3000,
                centerMode: false,
                centerPadding: '50px',
                cssEase: 'ease',
                customPaging: function(slider, i) {
                    return $('<button type="button" data-role="none" role="button" tabindex="0" />').text(i + 1);
                },
                dots: false,
                dotsClass: 'slick-dots',
                draggable: true,
                easing: 'linear',
                edgeFriction: 0.35,
                fade: false,
                focusOnSelect: false,
                infinite: true,
                initialSlide: 0,
                lazyLoad: 'ondemand',
                mobileFirst: false,
                pauseOnHover: true,
                pauseOnFocus: true,
                pauseOnDotsHover: false,
                respondTo: 'window',
                responsive: null,
                rows: 1,
                rtl: false,
                slide: '',
                slidesPerRow: 1,
                slidesToShow: 1,
                slidesToScroll: 1,
                speed: 500,
                swipe: true,
                swipeToSlide: false,
                touchMove: true,
                touchThreshold: 5,
                useCSS: true,
                useTransform: true,
                variableWidth: false,
                vertical: false,
                verticalSwiping: false,
                waitForAnimate: true,
                zIndex: 1000
            };

            _.initials = {
                animating: false,
                dragging: false,
                autoPlayTimer: null,
                currentDirection: 0,
                currentLeft: null,
                currentSlide: 0,
                direction: 1,
                $dots: null,
                listWidth: null,
                listHeight: null,
                loadIndex: 0,
                $nextArrow: null,
                $prevArrow: null,
                slideCount: null,
                slideWidth: null,
                $slideTrack: null,
                $slides: null,
                sliding: false,
                slideOffset: 0,
                swipeLeft: null,
                $list: null,
                touchObject: {},
                transformsEnabled: false,
                unslicked: false
            };

            $.extend(_, _.initials);

            _.activeBreakpoint = null;
            _.animType = null;
            _.animProp = null;
            _.breakpoints = [];
            _.breakpointSettings = [];
            _.cssTransitions = false;
            _.focussed = false;
            _.interrupted = false;
            _.hidden = 'hidden';
            _.paused = true;
            _.positionProp = null;
            _.respondTo = null;
            _.rowCount = 1;
            _.shouldClick = true;
            _.$slider = $(element);
            _.$slidesCache = null;
            _.transformType = null;
            _.transitionType = null;
            _.visibilityChange = 'visibilitychange';
            _.windowWidth = 0;
            _.windowTimer = null;

            dataSettings = $(element).data('slick') || {};

            _.options = $.extend({}, _.defaults, settings, dataSettings);

            _.currentSlide = _.options.initialSlide;

            _.originalSettings = _.options;

            if (typeof document.mozHidden !== 'undefined') {
                _.hidden = 'mozHidden';
                _.visibilityChange = 'mozvisibilitychange';
            } else if (typeof document.webkitHidden !== 'undefined') {
                _.hidden = 'webkitHidden';
                _.visibilityChange = 'webkitvisibilitychange';
            }

            _.autoPlay = $.proxy(_.autoPlay, _);
            _.autoPlayClear = $.proxy(_.autoPlayClear, _);
            _.autoPlayIterator = $.proxy(_.autoPlayIterator, _);
            _.changeSlide = $.proxy(_.changeSlide, _);
            _.clickHandler = $.proxy(_.clickHandler, _);
            _.selectHandler = $.proxy(_.selectHandler, _);
            _.setPosition = $.proxy(_.setPosition, _);
            _.swipeHandler = $.proxy(_.swipeHandler, _);
            _.dragHandler = $.proxy(_.dragHandler, _);
            _.keyHandler = $.proxy(_.keyHandler, _);

            _.instanceUid = instanceUid++;

            // A simple way to check for HTML strings
            // Strict HTML recognition (must start with <)
            // Extracted from jQuery v1.11 source
            _.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/;


            _.registerBreakpoints();
            _.init(true);

        }

        return Slick;

    }());

    Slick.prototype.activateADA = function() {
        var _ = this;

        _.$slideTrack.find('.slick-active').attr({
            'aria-hidden': 'false'
        }).find('a, input, button, select').attr({
            'tabindex': '0'
        });

    };

    Slick.prototype.addSlide = Slick.prototype.slickAdd = function(markup, index, addBefore) {

        var _ = this;

        if (typeof(index) === 'boolean') {
            addBefore = index;
            index = null;
        } else if (index < 0 || (index >= _.slideCount)) {
            return false;
        }

        _.unload();

        if (typeof(index) === 'number') {
            if (index === 0 && _.$slides.length === 0) {
                $(markup).appendTo(_.$slideTrack);
            } else if (addBefore) {
                $(markup).insertBefore(_.$slides.eq(index));
            } else {
                $(markup).insertAfter(_.$slides.eq(index));
            }
        } else {
            if (addBefore === true) {
                $(markup).prependTo(_.$slideTrack);
            } else {
                $(markup).appendTo(_.$slideTrack);
            }
        }

        _.$slides = _.$slideTrack.children(this.options.slide);

        _.$slideTrack.children(this.options.slide).detach();

        _.$slideTrack.append(_.$slides);

        _.$slides.each(function(index, element) {
            $(element).attr('data-slick-index', index);
        });

        _.$slidesCache = _.$slides;

        _.reinit();

    };

    Slick.prototype.animateHeight = function() {
        var _ = this;
        if (_.options.slidesToShow === 1 && _.options.adaptiveHeight === true && _.options.vertical === false) {
            var targetHeight = _.$slides.eq(_.currentSlide).outerHeight(true);
            _.$list.animate({
                height: targetHeight
            }, _.options.speed);
        }
    };

    Slick.prototype.animateSlide = function(targetLeft, callback) {

        var animProps = {},
            _ = this;

        _.animateHeight();

        if (_.options.rtl === true && _.options.vertical === false) {
            targetLeft = -targetLeft;
        }
        if (_.transformsEnabled === false) {
            if (_.options.vertical === false) {
                _.$slideTrack.animate({
                    left: targetLeft
                }, _.options.speed, _.options.easing, callback);
            } else {
                _.$slideTrack.animate({
                    top: targetLeft
                }, _.options.speed, _.options.easing, callback);
            }

        } else {

            if (_.cssTransitions === false) {
                if (_.options.rtl === true) {
                    _.currentLeft = -(_.currentLeft);
                }
                $({
                    animStart: _.currentLeft
                }).animate({
                    animStart: targetLeft
                }, {
                    duration: _.options.speed,
                    easing: _.options.easing,
                    step: function(now) {
                        now = Math.ceil(now);
                        if (_.options.vertical === false) {
                            animProps[_.animType] = 'translate(' +
                                now + 'px, 0px)';
                            _.$slideTrack.css(animProps);
                        } else {
                            animProps[_.animType] = 'translate(0px,' +
                                now + 'px)';
                            _.$slideTrack.css(animProps);
                        }
                    },
                    complete: function() {
                        if (callback) {
                            callback.call();
                        }
                    }
                });

            } else {

                _.applyTransition();
                targetLeft = Math.ceil(targetLeft);

                if (_.options.vertical === false) {
                    animProps[_.animType] = 'translate3d(' + targetLeft + 'px, 0px, 0px)';
                } else {
                    animProps[_.animType] = 'translate3d(0px,' + targetLeft + 'px, 0px)';
                }
                _.$slideTrack.css(animProps);

                if (callback) {
                    setTimeout(function() {

                        _.disableTransition();

                        callback.call();
                    }, _.options.speed);
                }

            }

        }

    };

    Slick.prototype.getNavTarget = function() {

        var _ = this,
            asNavFor = _.options.asNavFor;

        if ( asNavFor && asNavFor !== null ) {
            asNavFor = $(asNavFor).not(_.$slider);
        }

        return asNavFor;

    };

    Slick.prototype.asNavFor = function(index) {

        var _ = this,
            asNavFor = _.getNavTarget();

        if ( asNavFor !== null && typeof asNavFor === 'object' ) {
            asNavFor.each(function() {
                var target = $(this).slick('getSlick');
                if(!target.unslicked) {
                    target.slideHandler(index, true);
                }
            });
        }

    };

    Slick.prototype.applyTransition = function(slide) {

        var _ = this,
            transition = {};

        if (_.options.fade === false) {
            transition[_.transitionType] = _.transformType + ' ' + _.options.speed + 'ms ' + _.options.cssEase;
        } else {
            transition[_.transitionType] = 'opacity ' + _.options.speed + 'ms ' + _.options.cssEase;
        }

        if (_.options.fade === false) {
            _.$slideTrack.css(transition);
        } else {
            _.$slides.eq(slide).css(transition);
        }

    };

    Slick.prototype.autoPlay = function() {

        var _ = this;

        _.autoPlayClear();

        if ( _.slideCount > _.options.slidesToShow ) {
            _.autoPlayTimer = setInterval( _.autoPlayIterator, _.options.autoplaySpeed );
        }

    };

    Slick.prototype.autoPlayClear = function() {

        var _ = this;

        if (_.autoPlayTimer) {
            clearInterval(_.autoPlayTimer);
        }

    };

    Slick.prototype.autoPlayIterator = function() {

        var _ = this,
            slideTo = _.currentSlide + _.options.slidesToScroll;

        if ( !_.paused && !_.interrupted && !_.focussed ) {

            if ( _.options.infinite === false ) {

                if ( _.direction === 1 && ( _.currentSlide + 1 ) === ( _.slideCount - 1 )) {
                    _.direction = 0;
                }

                else if ( _.direction === 0 ) {

                    slideTo = _.currentSlide - _.options.slidesToScroll;

                    if ( _.currentSlide - 1 === 0 ) {
                        _.direction = 1;
                    }

                }

            }

            _.slideHandler( slideTo );

        }

    };

    Slick.prototype.buildArrows = function() {

        var _ = this;

        if (_.options.arrows === true ) {

            _.$prevArrow = $(_.options.prevArrow).addClass('slick-arrow');
            _.$nextArrow = $(_.options.nextArrow).addClass('slick-arrow');

            if( _.slideCount > _.options.slidesToShow ) {

                _.$prevArrow.removeClass('slick-hidden').removeAttr('aria-hidden tabindex');
                _.$nextArrow.removeClass('slick-hidden').removeAttr('aria-hidden tabindex');

                if (_.htmlExpr.test(_.options.prevArrow)) {
                    _.$prevArrow.prependTo(_.options.appendArrows);
                }

                if (_.htmlExpr.test(_.options.nextArrow)) {
                    _.$nextArrow.appendTo(_.options.appendArrows);
                }

                if (_.options.infinite !== true) {
                    _.$prevArrow
                        .addClass('slick-disabled')
                        .attr('aria-disabled', 'true');
                }

            } else {

                _.$prevArrow.add( _.$nextArrow )

                    .addClass('slick-hidden')
                    .attr({
                        'aria-disabled': 'true',
                        'tabindex': '-1'
                    });

            }

        }

    };

    Slick.prototype.buildDots = function() {

        var _ = this,
            i, dot;

        if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

            _.$slider.addClass('slick-dotted');

            dot = $('<ul />').addClass(_.options.dotsClass);

            for (i = 0; i <= _.getDotCount(); i += 1) {
                dot.append($('<li />').append(_.options.customPaging.call(this, _, i)));
            }

            _.$dots = dot.appendTo(_.options.appendDots);

            _.$dots.find('li').first().addClass('slick-active').attr('aria-hidden', 'false');

        }

    };

    Slick.prototype.buildOut = function() {

        var _ = this;

        _.$slides =
            _.$slider
                .children( _.options.slide + ':not(.slick-cloned)')
                .addClass('slick-slide');

        _.slideCount = _.$slides.length;

        _.$slides.each(function(index, element) {
            $(element)
                .attr('data-slick-index', index)
                .data('originalStyling', $(element).attr('style') || '');
        });

        _.$slider.addClass('slick-slider');

        _.$slideTrack = (_.slideCount === 0) ?
            $('<div class="slick-track"/>').appendTo(_.$slider) :
            _.$slides.wrapAll('<div class="slick-track"/>').parent();

        _.$list = _.$slideTrack.wrap(
            '<div aria-live="polite" class="slick-list"/>').parent();
        _.$slideTrack.css('opacity', 0);

        if (_.options.centerMode === true || _.options.swipeToSlide === true) {
            _.options.slidesToScroll = 1;
        }

        $('img[data-lazy]', _.$slider).not('[src]').addClass('slick-loading');

        _.setupInfinite();

        _.buildArrows();

        _.buildDots();

        _.updateDots();


        _.setSlideClasses(typeof _.currentSlide === 'number' ? _.currentSlide : 0);

        if (_.options.draggable === true) {
            _.$list.addClass('draggable');
        }

    };

    Slick.prototype.buildRows = function() {

        var _ = this, a, b, c, newSlides, numOfSlides, originalSlides,slidesPerSection;

        newSlides = document.createDocumentFragment();
        originalSlides = _.$slider.children();

        if(_.options.rows > 1) {

            slidesPerSection = _.options.slidesPerRow * _.options.rows;
            numOfSlides = Math.ceil(
                originalSlides.length / slidesPerSection
            );

            for(a = 0; a < numOfSlides; a++){
                var slide = document.createElement('div');
                for(b = 0; b < _.options.rows; b++) {
                    var row = document.createElement('div');
                    for(c = 0; c < _.options.slidesPerRow; c++) {
                        var target = (a * slidesPerSection + ((b * _.options.slidesPerRow) + c));
                        if (originalSlides.get(target)) {
                            row.appendChild(originalSlides.get(target));
                        }
                    }
                    slide.appendChild(row);
                }
                newSlides.appendChild(slide);
            }

            _.$slider.empty().append(newSlides);
            _.$slider.children().children().children()
                .css({
                    'width':(100 / _.options.slidesPerRow) + '%',
                    'display': 'inline-block'
                });

        }

    };

    Slick.prototype.checkResponsive = function(initial, forceUpdate) {

        var _ = this,
            breakpoint, targetBreakpoint, respondToWidth, triggerBreakpoint = false;
        var sliderWidth = _.$slider.width();
        var windowWidth = window.innerWidth || $(window).width();

        if (_.respondTo === 'window') {
            respondToWidth = windowWidth;
        } else if (_.respondTo === 'slider') {
            respondToWidth = sliderWidth;
        } else if (_.respondTo === 'min') {
            respondToWidth = Math.min(windowWidth, sliderWidth);
        }

        if ( _.options.responsive &&
            _.options.responsive.length &&
            _.options.responsive !== null) {

            targetBreakpoint = null;

            for (breakpoint in _.breakpoints) {
                if (_.breakpoints.hasOwnProperty(breakpoint)) {
                    if (_.originalSettings.mobileFirst === false) {
                        if (respondToWidth < _.breakpoints[breakpoint]) {
                            targetBreakpoint = _.breakpoints[breakpoint];
                        }
                    } else {
                        if (respondToWidth > _.breakpoints[breakpoint]) {
                            targetBreakpoint = _.breakpoints[breakpoint];
                        }
                    }
                }
            }

            if (targetBreakpoint !== null) {
                if (_.activeBreakpoint !== null) {
                    if (targetBreakpoint !== _.activeBreakpoint || forceUpdate) {
                        _.activeBreakpoint =
                            targetBreakpoint;
                        if (_.breakpointSettings[targetBreakpoint] === 'unslick') {
                            _.unslick(targetBreakpoint);
                        } else {
                            _.options = $.extend({}, _.originalSettings,
                                _.breakpointSettings[
                                    targetBreakpoint]);
                            if (initial === true) {
                                _.currentSlide = _.options.initialSlide;
                            }
                            _.refresh(initial);
                        }
                        triggerBreakpoint = targetBreakpoint;
                    }
                } else {
                    _.activeBreakpoint = targetBreakpoint;
                    if (_.breakpointSettings[targetBreakpoint] === 'unslick') {
                        _.unslick(targetBreakpoint);
                    } else {
                        _.options = $.extend({}, _.originalSettings,
                            _.breakpointSettings[
                                targetBreakpoint]);
                        if (initial === true) {
                            _.currentSlide = _.options.initialSlide;
                        }
                        _.refresh(initial);
                    }
                    triggerBreakpoint = targetBreakpoint;
                }
            } else {
                if (_.activeBreakpoint !== null) {
                    _.activeBreakpoint = null;
                    _.options = _.originalSettings;
                    if (initial === true) {
                        _.currentSlide = _.options.initialSlide;
                    }
                    _.refresh(initial);
                    triggerBreakpoint = targetBreakpoint;
                }
            }

            // only trigger breakpoints during an actual break. not on initialize.
            if( !initial && triggerBreakpoint !== false ) {
                _.$slider.trigger('breakpoint', [_, triggerBreakpoint]);
            }
        }

    };

    Slick.prototype.changeSlide = function(event, dontAnimate) {

        var _ = this,
            $target = $(event.currentTarget),
            indexOffset, slideOffset, unevenOffset;

        // If target is a link, prevent default action.
        if($target.is('a')) {
            event.preventDefault();
        }

        // If target is not the <li> element (ie: a child), find the <li>.
        if(!$target.is('li')) {
            $target = $target.closest('li');
        }

        unevenOffset = (_.slideCount % _.options.slidesToScroll !== 0);
        indexOffset = unevenOffset ? 0 : (_.slideCount - _.currentSlide) % _.options.slidesToScroll;

        switch (event.data.message) {

            case 'previous':
                slideOffset = indexOffset === 0 ? _.options.slidesToScroll : _.options.slidesToShow - indexOffset;
                if (_.slideCount > _.options.slidesToShow) {
                    _.slideHandler(_.currentSlide - slideOffset, false, dontAnimate);
                }
                break;

            case 'next':
                slideOffset = indexOffset === 0 ? _.options.slidesToScroll : indexOffset;
                if (_.slideCount > _.options.slidesToShow) {
                    _.slideHandler(_.currentSlide + slideOffset, false, dontAnimate);
                }
                break;

            case 'index':
                var index = event.data.index === 0 ? 0 :
                    event.data.index || $target.index() * _.options.slidesToScroll;

                _.slideHandler(_.checkNavigable(index), false, dontAnimate);
                $target.children().trigger('focus');
                break;

            default:
                return;
        }

    };

    Slick.prototype.checkNavigable = function(index) {

        var _ = this,
            navigables, prevNavigable;

        navigables = _.getNavigableIndexes();
        prevNavigable = 0;
        if (index > navigables[navigables.length - 1]) {
            index = navigables[navigables.length - 1];
        } else {
            for (var n in navigables) {
                if (index < navigables[n]) {
                    index = prevNavigable;
                    break;
                }
                prevNavigable = navigables[n];
            }
        }

        return index;
    };

    Slick.prototype.cleanUpEvents = function() {

        var _ = this;

        if (_.options.dots && _.$dots !== null) {

            $('li', _.$dots)
                .off('click.slick', _.changeSlide)
                .off('mouseenter.slick', $.proxy(_.interrupt, _, true))
                .off('mouseleave.slick', $.proxy(_.interrupt, _, false));

        }

        _.$slider.off('focus.slick blur.slick');

        if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {
            _.$prevArrow && _.$prevArrow.off('click.slick', _.changeSlide);
            _.$nextArrow && _.$nextArrow.off('click.slick', _.changeSlide);
        }

        _.$list.off('touchstart.slick mousedown.slick', _.swipeHandler);
        _.$list.off('touchmove.slick mousemove.slick', _.swipeHandler);
        _.$list.off('touchend.slick mouseup.slick', _.swipeHandler);
        _.$list.off('touchcancel.slick mouseleave.slick', _.swipeHandler);

        _.$list.off('click.slick', _.clickHandler);

        $(document).off(_.visibilityChange, _.visibility);

        _.cleanUpSlideEvents();

        if (_.options.accessibility === true) {
            _.$list.off('keydown.slick', _.keyHandler);
        }

        if (_.options.focusOnSelect === true) {
            $(_.$slideTrack).children().off('click.slick', _.selectHandler);
        }

        $(window).off('orientationchange.slick.slick-' + _.instanceUid, _.orientationChange);

        $(window).off('resize.slick.slick-' + _.instanceUid, _.resize);

        $('[draggable!=true]', _.$slideTrack).off('dragstart', _.preventDefault);

        $(window).off('load.slick.slick-' + _.instanceUid, _.setPosition);
        $(document).off('ready.slick.slick-' + _.instanceUid, _.setPosition);

    };

    Slick.prototype.cleanUpSlideEvents = function() {

        var _ = this;

        _.$list.off('mouseenter.slick', $.proxy(_.interrupt, _, true));
        _.$list.off('mouseleave.slick', $.proxy(_.interrupt, _, false));

    };

    Slick.prototype.cleanUpRows = function() {

        var _ = this, originalSlides;

        if(_.options.rows > 1) {
            originalSlides = _.$slides.children().children();
            originalSlides.removeAttr('style');
            _.$slider.empty().append(originalSlides);
        }

    };

    Slick.prototype.clickHandler = function(event) {

        var _ = this;

        if (_.shouldClick === false) {
            event.stopImmediatePropagation();
            event.stopPropagation();
            event.preventDefault();
        }

    };

    Slick.prototype.destroy = function(refresh) {

        var _ = this;

        _.autoPlayClear();

        _.touchObject = {};

        _.cleanUpEvents();

        $('.slick-cloned', _.$slider).detach();

        if (_.$dots) {
            _.$dots.remove();
        }


        if ( _.$prevArrow && _.$prevArrow.length ) {

            _.$prevArrow
                .removeClass('slick-disabled slick-arrow slick-hidden')
                .removeAttr('aria-hidden aria-disabled tabindex')
                .css('display','');

            if ( _.htmlExpr.test( _.options.prevArrow )) {
                _.$prevArrow.remove();
            }
        }

        if ( _.$nextArrow && _.$nextArrow.length ) {

            _.$nextArrow
                .removeClass('slick-disabled slick-arrow slick-hidden')
                .removeAttr('aria-hidden aria-disabled tabindex')
                .css('display','');

            if ( _.htmlExpr.test( _.options.nextArrow )) {
                _.$nextArrow.remove();
            }

        }


        if (_.$slides) {

            _.$slides
                .removeClass('slick-slide slick-active slick-center slick-visible slick-current')
                .removeAttr('aria-hidden')
                .removeAttr('data-slick-index')
                .each(function(){
                    $(this).attr('style', $(this).data('originalStyling'));
                });

            _.$slideTrack.children(this.options.slide).detach();

            _.$slideTrack.detach();

            _.$list.detach();

            _.$slider.append(_.$slides);
        }

        _.cleanUpRows();

        _.$slider.removeClass('slick-slider');
        _.$slider.removeClass('slick-initialized');
        _.$slider.removeClass('slick-dotted');

        _.unslicked = true;

        if(!refresh) {
            _.$slider.trigger('destroy', [_]);
        }

    };

    Slick.prototype.disableTransition = function(slide) {

        var _ = this,
            transition = {};

        transition[_.transitionType] = '';

        if (_.options.fade === false) {
            _.$slideTrack.css(transition);
        } else {
            _.$slides.eq(slide).css(transition);
        }

    };

    Slick.prototype.fadeSlide = function(slideIndex, callback) {

        var _ = this;

        if (_.cssTransitions === false) {

            _.$slides.eq(slideIndex).css({
                zIndex: _.options.zIndex
            });

            _.$slides.eq(slideIndex).animate({
                opacity: 1
            }, _.options.speed, _.options.easing, callback);

        } else {

            _.applyTransition(slideIndex);

            _.$slides.eq(slideIndex).css({
                opacity: 1,
                zIndex: _.options.zIndex
            });

            if (callback) {
                setTimeout(function() {

                    _.disableTransition(slideIndex);

                    callback.call();
                }, _.options.speed);
            }

        }

    };

    Slick.prototype.fadeSlideOut = function(slideIndex) {

        var _ = this;

        if (_.cssTransitions === false) {

            _.$slides.eq(slideIndex).animate({
                opacity: 0,
                zIndex: _.options.zIndex - 2
            }, _.options.speed, _.options.easing);

        } else {

            _.applyTransition(slideIndex);

            _.$slides.eq(slideIndex).css({
                opacity: 0,
                zIndex: _.options.zIndex - 2
            });

        }

    };

    Slick.prototype.filterSlides = Slick.prototype.slickFilter = function(filter) {

        var _ = this;

        if (filter !== null) {

            _.$slidesCache = _.$slides;

            _.unload();

            _.$slideTrack.children(this.options.slide).detach();

            _.$slidesCache.filter(filter).appendTo(_.$slideTrack);

            _.reinit();

        }

    };

    Slick.prototype.focusHandler = function() {

        var _ = this;

        _.$slider
            .off('focus.slick blur.slick')
            .on('focus.slick blur.slick',
                '*:not(.slick-arrow)', function(event) {

            event.stopImmediatePropagation();
            var $sf = $(this);

            setTimeout(function() {

                if( _.options.pauseOnFocus ) {
                    _.focussed = $sf.is(':focus');
                    _.autoPlay();
                }

            }, 0);

        });
    };

    Slick.prototype.getCurrent = Slick.prototype.slickCurrentSlide = function() {

        var _ = this;
        return _.currentSlide;

    };

    Slick.prototype.getDotCount = function() {

        var _ = this;

        var breakPoint = 0;
        var counter = 0;
        var pagerQty = 0;

        if (_.options.infinite === true) {
            while (breakPoint < _.slideCount) {
                ++pagerQty;
                breakPoint = counter + _.options.slidesToScroll;
                counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
            }
        } else if (_.options.centerMode === true) {
            pagerQty = _.slideCount;
        } else if(!_.options.asNavFor) {
            pagerQty = 1 + Math.ceil((_.slideCount - _.options.slidesToShow) / _.options.slidesToScroll);
        }else {
            while (breakPoint < _.slideCount) {
                ++pagerQty;
                breakPoint = counter + _.options.slidesToScroll;
                counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
            }
        }

        return pagerQty - 1;

    };

    Slick.prototype.getLeft = function(slideIndex) {

        var _ = this,
            targetLeft,
            verticalHeight,
            verticalOffset = 0,
            targetSlide;

        _.slideOffset = 0;
        verticalHeight = _.$slides.first().outerHeight(true);

        if (_.options.infinite === true) {
            if (_.slideCount > _.options.slidesToShow) {
                _.slideOffset = (_.slideWidth * _.options.slidesToShow) * -1;
                verticalOffset = (verticalHeight * _.options.slidesToShow) * -1;
            }
            if (_.slideCount % _.options.slidesToScroll !== 0) {
                if (slideIndex + _.options.slidesToScroll > _.slideCount && _.slideCount > _.options.slidesToShow) {
                    if (slideIndex > _.slideCount) {
                        _.slideOffset = ((_.options.slidesToShow - (slideIndex - _.slideCount)) * _.slideWidth) * -1;
                        verticalOffset = ((_.options.slidesToShow - (slideIndex - _.slideCount)) * verticalHeight) * -1;
                    } else {
                        _.slideOffset = ((_.slideCount % _.options.slidesToScroll) * _.slideWidth) * -1;
                        verticalOffset = ((_.slideCount % _.options.slidesToScroll) * verticalHeight) * -1;
                    }
                }
            }
        } else {
            if (slideIndex + _.options.slidesToShow > _.slideCount) {
                _.slideOffset = ((slideIndex + _.options.slidesToShow) - _.slideCount) * _.slideWidth;
                verticalOffset = ((slideIndex + _.options.slidesToShow) - _.slideCount) * verticalHeight;
            }
        }

        if (_.slideCount <= _.options.slidesToShow) {
            _.slideOffset = 0;
            verticalOffset = 0;
        }

        if (_.options.centerMode === true && _.options.infinite === true) {
            _.slideOffset += _.slideWidth * Math.floor(_.options.slidesToShow / 2) - _.slideWidth;
        } else if (_.options.centerMode === true) {
            _.slideOffset = 0;
            _.slideOffset += _.slideWidth * Math.floor(_.options.slidesToShow / 2);
        }

        if (_.options.vertical === false) {
            targetLeft = ((slideIndex * _.slideWidth) * -1) + _.slideOffset;
        } else {
            targetLeft = ((slideIndex * verticalHeight) * -1) + verticalOffset;
        }

        if (_.options.variableWidth === true) {

            if (_.slideCount <= _.options.slidesToShow || _.options.infinite === false) {
                targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex);
            } else {
                targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex + _.options.slidesToShow);
            }

            if (_.options.rtl === true) {
                if (targetSlide[0]) {
                    targetLeft = (_.$slideTrack.width() - targetSlide[0].offsetLeft - targetSlide.width()) * -1;
                } else {
                    targetLeft =  0;
                }
            } else {
                targetLeft = targetSlide[0] ? targetSlide[0].offsetLeft * -1 : 0;
            }

            if (_.options.centerMode === true) {
                if (_.slideCount <= _.options.slidesToShow || _.options.infinite === false) {
                    targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex);
                } else {
                    targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex + _.options.slidesToShow + 1);
                }

                if (_.options.rtl === true) {
                    if (targetSlide[0]) {
                        targetLeft = (_.$slideTrack.width() - targetSlide[0].offsetLeft - targetSlide.width()) * -1;
                    } else {
                        targetLeft =  0;
                    }
                } else {
                    targetLeft = targetSlide[0] ? targetSlide[0].offsetLeft * -1 : 0;
                }

                targetLeft += (_.$list.width() - targetSlide.outerWidth()) / 2;
            }
        }

        return targetLeft;

    };

    Slick.prototype.getOption = Slick.prototype.slickGetOption = function(option) {

        var _ = this;

        return _.options[option];

    };

    Slick.prototype.getNavigableIndexes = function() {

        var _ = this,
            breakPoint = 0,
            counter = 0,
            indexes = [],
            max;

        if (_.options.infinite === false) {
            max = _.slideCount;
        } else {
            breakPoint = _.options.slidesToScroll * -1;
            counter = _.options.slidesToScroll * -1;
            max = _.slideCount * 2;
        }

        while (breakPoint < max) {
            indexes.push(breakPoint);
            breakPoint = counter + _.options.slidesToScroll;
            counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
        }

        return indexes;

    };

    Slick.prototype.getSlick = function() {

        return this;

    };

    Slick.prototype.getSlideCount = function() {

        var _ = this,
            slidesTraversed, swipedSlide, centerOffset;

        centerOffset = _.options.centerMode === true ? _.slideWidth * Math.floor(_.options.slidesToShow / 2) : 0;

        if (_.options.swipeToSlide === true) {
            _.$slideTrack.find('.slick-slide').each(function(index, slide) {
                if (slide.offsetLeft - centerOffset + ($(slide).outerWidth() / 2) > (_.swipeLeft * -1)) {
                    swipedSlide = slide;
                    return false;
                }
            });

            slidesTraversed = Math.abs($(swipedSlide).attr('data-slick-index') - _.currentSlide) || 1;

            return slidesTraversed;

        } else {
            return _.options.slidesToScroll;
        }

    };

    Slick.prototype.goTo = Slick.prototype.slickGoTo = function(slide, dontAnimate) {

        var _ = this;

        _.changeSlide({
            data: {
                message: 'index',
                index: parseInt(slide)
            }
        }, dontAnimate);

    };

    Slick.prototype.init = function(creation) {

        var _ = this;

        if (!$(_.$slider).hasClass('slick-initialized')) {

            $(_.$slider).addClass('slick-initialized');

            _.buildRows();
            _.buildOut();
            _.setProps();
            _.startLoad();
            _.loadSlider();
            _.initializeEvents();
            _.updateArrows();
            _.updateDots();
            _.checkResponsive(true);
            _.focusHandler();

        }

        if (creation) {
            _.$slider.trigger('init', [_]);
        }

        if (_.options.accessibility === true) {
            _.initADA();
        }

        if ( _.options.autoplay ) {

            _.paused = false;
            _.autoPlay();

        }

    };

    Slick.prototype.initADA = function() {
        var _ = this;
        _.$slides.add(_.$slideTrack.find('.slick-cloned')).attr({
            'aria-hidden': 'true',
            'tabindex': '-1'
        }).find('a, input, button, select').attr({
            'tabindex': '-1'
        });

        _.$slideTrack.attr('role', 'listbox');

        _.$slides.not(_.$slideTrack.find('.slick-cloned')).each(function(i) {
            $(this).attr({
                'role': 'option',
                'aria-describedby': 'slick-slide' + _.instanceUid + i + ''
            });
        });

        if (_.$dots !== null) {
            _.$dots.attr('role', 'tablist').find('li').each(function(i) {
                $(this).attr({
                    'role': 'presentation',
                    'aria-selected': 'false',
                    'aria-controls': 'navigation' + _.instanceUid + i + '',
                    'id': 'slick-slide' + _.instanceUid + i + ''
                });
            })
                .first().attr('aria-selected', 'true').end()
                .find('button').attr('role', 'button').end()
                .closest('div').attr('role', 'toolbar');
        }
        _.activateADA();

    };

    Slick.prototype.initArrowEvents = function() {

        var _ = this;

        if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {
            _.$prevArrow
               .off('click.slick')
               .on('click.slick', {
                    message: 'previous'
               }, _.changeSlide);
            _.$nextArrow
               .off('click.slick')
               .on('click.slick', {
                    message: 'next'
               }, _.changeSlide);
        }

    };

    Slick.prototype.initDotEvents = function() {

        var _ = this;

        if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {
            $('li', _.$dots).on('click.slick', {
                message: 'index'
            }, _.changeSlide);
        }

        if ( _.options.dots === true && _.options.pauseOnDotsHover === true ) {

            $('li', _.$dots)
                .on('mouseenter.slick', $.proxy(_.interrupt, _, true))
                .on('mouseleave.slick', $.proxy(_.interrupt, _, false));

        }

    };

    Slick.prototype.initSlideEvents = function() {

        var _ = this;

        if ( _.options.pauseOnHover ) {

            _.$list.on('mouseenter.slick', $.proxy(_.interrupt, _, true));
            _.$list.on('mouseleave.slick', $.proxy(_.interrupt, _, false));

        }

    };

    Slick.prototype.initializeEvents = function() {

        var _ = this;

        _.initArrowEvents();

        _.initDotEvents();
        _.initSlideEvents();

        _.$list.on('touchstart.slick mousedown.slick', {
            action: 'start'
        }, _.swipeHandler);
        _.$list.on('touchmove.slick mousemove.slick', {
            action: 'move'
        }, _.swipeHandler);
        _.$list.on('touchend.slick mouseup.slick', {
            action: 'end'
        }, _.swipeHandler);
        _.$list.on('touchcancel.slick mouseleave.slick', {
            action: 'end'
        }, _.swipeHandler);

        _.$list.on('click.slick', _.clickHandler);

        $(document).on(_.visibilityChange, $.proxy(_.visibility, _));

        if (_.options.accessibility === true) {
            _.$list.on('keydown.slick', _.keyHandler);
        }

        if (_.options.focusOnSelect === true) {
            $(_.$slideTrack).children().on('click.slick', _.selectHandler);
        }

        $(window).on('orientationchange.slick.slick-' + _.instanceUid, $.proxy(_.orientationChange, _));

        $(window).on('resize.slick.slick-' + _.instanceUid, $.proxy(_.resize, _));

        $('[draggable!=true]', _.$slideTrack).on('dragstart', _.preventDefault);

        $(window).on('load.slick.slick-' + _.instanceUid, _.setPosition);
        $(document).on('ready.slick.slick-' + _.instanceUid, _.setPosition);

    };

    Slick.prototype.initUI = function() {

        var _ = this;

        if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {

            _.$prevArrow.show();
            _.$nextArrow.show();

        }

        if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

            _.$dots.show();

        }

    };

    Slick.prototype.keyHandler = function(event) {

        var _ = this;
         //Dont slide if the cursor is inside the form fields and arrow keys are pressed
        if(!event.target.tagName.match('TEXTAREA|INPUT|SELECT')) {
            if (event.keyCode === 37 && _.options.accessibility === true) {
                _.changeSlide({
                    data: {
                        message: _.options.rtl === true ? 'next' :  'previous'
                    }
                });
            } else if (event.keyCode === 39 && _.options.accessibility === true) {
                _.changeSlide({
                    data: {
                        message: _.options.rtl === true ? 'previous' : 'next'
                    }
                });
            }
        }

    };

    Slick.prototype.lazyLoad = function() {

        var _ = this,
            loadRange, cloneRange, rangeStart, rangeEnd;

        function loadImages(imagesScope) {

            $('img[data-lazy]', imagesScope).each(function() {

                var image = $(this),
                    imageSource = $(this).attr('data-lazy'),
                    imageToLoad = document.createElement('img');

                imageToLoad.onload = function() {

                    image
                        .animate({ opacity: 0 }, 100, function() {
                            image
                                .attr('src', imageSource)
                                .animate({ opacity: 1 }, 200, function() {
                                    image
                                        .removeAttr('data-lazy')
                                        .removeClass('slick-loading');
                                });
                            _.$slider.trigger('lazyLoaded', [_, image, imageSource]);
                        });

                };

                imageToLoad.onerror = function() {

                    image
                        .removeAttr( 'data-lazy' )
                        .removeClass( 'slick-loading' )
                        .addClass( 'slick-lazyload-error' );

                    _.$slider.trigger('lazyLoadError', [ _, image, imageSource ]);

                };

                imageToLoad.src = imageSource;

            });

        }

        if (_.options.centerMode === true) {
            if (_.options.infinite === true) {
                rangeStart = _.currentSlide + (_.options.slidesToShow / 2 + 1);
                rangeEnd = rangeStart + _.options.slidesToShow + 2;
            } else {
                rangeStart = Math.max(0, _.currentSlide - (_.options.slidesToShow / 2 + 1));
                rangeEnd = 2 + (_.options.slidesToShow / 2 + 1) + _.currentSlide;
            }
        } else {
            rangeStart = _.options.infinite ? _.options.slidesToShow + _.currentSlide : _.currentSlide;
            rangeEnd = Math.ceil(rangeStart + _.options.slidesToShow);
            if (_.options.fade === true) {
                if (rangeStart > 0) rangeStart--;
                if (rangeEnd <= _.slideCount) rangeEnd++;
            }
        }

        loadRange = _.$slider.find('.slick-slide').slice(rangeStart, rangeEnd);
        loadImages(loadRange);

        if (_.slideCount <= _.options.slidesToShow) {
            cloneRange = _.$slider.find('.slick-slide');
            loadImages(cloneRange);
        } else
        if (_.currentSlide >= _.slideCount - _.options.slidesToShow) {
            cloneRange = _.$slider.find('.slick-cloned').slice(0, _.options.slidesToShow);
            loadImages(cloneRange);
        } else if (_.currentSlide === 0) {
            cloneRange = _.$slider.find('.slick-cloned').slice(_.options.slidesToShow * -1);
            loadImages(cloneRange);
        }

    };

    Slick.prototype.loadSlider = function() {

        var _ = this;

        _.setPosition();

        _.$slideTrack.css({
            opacity: 1
        });

        _.$slider.removeClass('slick-loading');

        _.initUI();

        if (_.options.lazyLoad === 'progressive') {
            _.progressiveLazyLoad();
        }

    };

    Slick.prototype.next = Slick.prototype.slickNext = function() {

        var _ = this;

        _.changeSlide({
            data: {
                message: 'next'
            }
        });

    };

    Slick.prototype.orientationChange = function() {

        var _ = this;

        _.checkResponsive();
        _.setPosition();

    };

    Slick.prototype.pause = Slick.prototype.slickPause = function() {

        var _ = this;

        _.autoPlayClear();
        _.paused = true;

    };

    Slick.prototype.play = Slick.prototype.slickPlay = function() {

        var _ = this;

        _.autoPlay();
        _.options.autoplay = true;
        _.paused = false;
        _.focussed = false;
        _.interrupted = false;

    };

    Slick.prototype.postSlide = function(index) {

        var _ = this;

        if( !_.unslicked ) {

            _.$slider.trigger('afterChange', [_, index]);

            _.animating = false;

            _.setPosition();

            _.swipeLeft = null;

            if ( _.options.autoplay ) {
                _.autoPlay();
            }

            if (_.options.accessibility === true) {
                _.initADA();
            }

        }

    };

    Slick.prototype.prev = Slick.prototype.slickPrev = function() {

        var _ = this;

        _.changeSlide({
            data: {
                message: 'previous'
            }
        });

    };

    Slick.prototype.preventDefault = function(event) {

        event.preventDefault();

    };

    Slick.prototype.progressiveLazyLoad = function( tryCount ) {

        tryCount = tryCount || 1;

        var _ = this,
            $imgsToLoad = $( 'img[data-lazy]', _.$slider ),
            image,
            imageSource,
            imageToLoad;

        if ( $imgsToLoad.length ) {

            image = $imgsToLoad.first();
            imageSource = image.attr('data-lazy');
            imageToLoad = document.createElement('img');

            imageToLoad.onload = function() {

                image
                    .attr( 'src', imageSource )
                    .removeAttr('data-lazy')
                    .removeClass('slick-loading');

                if ( _.options.adaptiveHeight === true ) {
                    _.setPosition();
                }

                _.$slider.trigger('lazyLoaded', [ _, image, imageSource ]);
                _.progressiveLazyLoad();

            };

            imageToLoad.onerror = function() {

                if ( tryCount < 3 ) {

                    /**
                     * try to load the image 3 times,
                     * leave a slight delay so we don't get
                     * servers blocking the request.
                     */
                    setTimeout( function() {
                        _.progressiveLazyLoad( tryCount + 1 );
                    }, 500 );

                } else {

                    image
                        .removeAttr( 'data-lazy' )
                        .removeClass( 'slick-loading' )
                        .addClass( 'slick-lazyload-error' );

                    _.$slider.trigger('lazyLoadError', [ _, image, imageSource ]);

                    _.progressiveLazyLoad();

                }

            };

            imageToLoad.src = imageSource;

        } else {

            _.$slider.trigger('allImagesLoaded', [ _ ]);

        }

    };

    Slick.prototype.refresh = function( initializing ) {

        var _ = this, currentSlide, lastVisibleIndex;

        lastVisibleIndex = _.slideCount - _.options.slidesToShow;

        // in non-infinite sliders, we don't want to go past the
        // last visible index.
        if( !_.options.infinite && ( _.currentSlide > lastVisibleIndex )) {
            _.currentSlide = lastVisibleIndex;
        }

        // if less slides than to show, go to start.
        if ( _.slideCount <= _.options.slidesToShow ) {
            _.currentSlide = 0;

        }

        currentSlide = _.currentSlide;

        _.destroy(true);

        $.extend(_, _.initials, { currentSlide: currentSlide });

        _.init();

        if( !initializing ) {

            _.changeSlide({
                data: {
                    message: 'index',
                    index: currentSlide
                }
            }, false);

        }

    };

    Slick.prototype.registerBreakpoints = function() {

        var _ = this, breakpoint, currentBreakpoint, l,
            responsiveSettings = _.options.responsive || null;

        if ( $.type(responsiveSettings) === 'array' && responsiveSettings.length ) {

            _.respondTo = _.options.respondTo || 'window';

            for ( breakpoint in responsiveSettings ) {

                l = _.breakpoints.length-1;
                currentBreakpoint = responsiveSettings[breakpoint].breakpoint;

                if (responsiveSettings.hasOwnProperty(breakpoint)) {

                    // loop through the breakpoints and cut out any existing
                    // ones with the same breakpoint number, we don't want dupes.
                    while( l >= 0 ) {
                        if( _.breakpoints[l] && _.breakpoints[l] === currentBreakpoint ) {
                            _.breakpoints.splice(l,1);
                        }
                        l--;
                    }

                    _.breakpoints.push(currentBreakpoint);
                    _.breakpointSettings[currentBreakpoint] = responsiveSettings[breakpoint].settings;

                }

            }

            _.breakpoints.sort(function(a, b) {
                return ( _.options.mobileFirst ) ? a-b : b-a;
            });

        }

    };

    Slick.prototype.reinit = function() {

        var _ = this;

        _.$slides =
            _.$slideTrack
                .children(_.options.slide)
                .addClass('slick-slide');

        _.slideCount = _.$slides.length;

        if (_.currentSlide >= _.slideCount && _.currentSlide !== 0) {
            _.currentSlide = _.currentSlide - _.options.slidesToScroll;
        }

        if (_.slideCount <= _.options.slidesToShow) {
            _.currentSlide = 0;
        }

        _.registerBreakpoints();

        _.setProps();
        _.setupInfinite();
        _.buildArrows();
        _.updateArrows();
        _.initArrowEvents();
        _.buildDots();
        _.updateDots();
        _.initDotEvents();
        _.cleanUpSlideEvents();
        _.initSlideEvents();

        _.checkResponsive(false, true);

        if (_.options.focusOnSelect === true) {
            $(_.$slideTrack).children().on('click.slick', _.selectHandler);
        }

        _.setSlideClasses(typeof _.currentSlide === 'number' ? _.currentSlide : 0);

        _.setPosition();
        _.focusHandler();

        _.paused = !_.options.autoplay;
        _.autoPlay();

        _.$slider.trigger('reInit', [_]);

    };

    Slick.prototype.resize = function() {

        var _ = this;

        if ($(window).width() !== _.windowWidth) {
            clearTimeout(_.windowDelay);
            _.windowDelay = window.setTimeout(function() {
                _.windowWidth = $(window).width();
                _.checkResponsive();
                if( !_.unslicked ) { _.setPosition(); }
            }, 50);
        }
    };

    Slick.prototype.removeSlide = Slick.prototype.slickRemove = function(index, removeBefore, removeAll) {

        var _ = this;

        if (typeof(index) === 'boolean') {
            removeBefore = index;
            index = removeBefore === true ? 0 : _.slideCount - 1;
        } else {
            index = removeBefore === true ? --index : index;
        }

        if (_.slideCount < 1 || index < 0 || index > _.slideCount - 1) {
            return false;
        }

        _.unload();

        if (removeAll === true) {
            _.$slideTrack.children().remove();
        } else {
            _.$slideTrack.children(this.options.slide).eq(index).remove();
        }

        _.$slides = _.$slideTrack.children(this.options.slide);

        _.$slideTrack.children(this.options.slide).detach();

        _.$slideTrack.append(_.$slides);

        _.$slidesCache = _.$slides;

        _.reinit();

    };

    Slick.prototype.setCSS = function(position) {

        var _ = this,
            positionProps = {},
            x, y;

        if (_.options.rtl === true) {
            position = -position;
        }
        x = _.positionProp == 'left' ? Math.ceil(position) + 'px' : '0px';
        y = _.positionProp == 'top' ? Math.ceil(position) + 'px' : '0px';

        positionProps[_.positionProp] = position;

        if (_.transformsEnabled === false) {
            _.$slideTrack.css(positionProps);
        } else {
            positionProps = {};
            if (_.cssTransitions === false) {
                positionProps[_.animType] = 'translate(' + x + ', ' + y + ')';
                _.$slideTrack.css(positionProps);
            } else {
                positionProps[_.animType] = 'translate3d(' + x + ', ' + y + ', 0px)';
                _.$slideTrack.css(positionProps);
            }
        }

    };

    Slick.prototype.setDimensions = function() {

        var _ = this;

        if (_.options.vertical === false) {
            if (_.options.centerMode === true) {
                _.$list.css({
                    padding: ('0px ' + _.options.centerPadding)
                });
            }
        } else {
            _.$list.height(_.$slides.first().outerHeight(true) * _.options.slidesToShow);
            if (_.options.centerMode === true) {
                _.$list.css({
                    padding: (_.options.centerPadding + ' 0px')
                });
            }
        }

        _.listWidth = _.$list.width();
        _.listHeight = _.$list.height();


        if (_.options.vertical === false && _.options.variableWidth === false) {
            _.slideWidth = Math.ceil(_.listWidth / _.options.slidesToShow);
            _.$slideTrack.width(Math.ceil((_.slideWidth * _.$slideTrack.children('.slick-slide').length)));

        } else if (_.options.variableWidth === true) {
            _.$slideTrack.width(5000 * _.slideCount);
        } else {
            _.slideWidth = Math.ceil(_.listWidth);
            _.$slideTrack.height(Math.ceil((_.$slides.first().outerHeight(true) * _.$slideTrack.children('.slick-slide').length)));
        }

        var offset = _.$slides.first().outerWidth(true) - _.$slides.first().width();
        if (_.options.variableWidth === false) _.$slideTrack.children('.slick-slide').width(_.slideWidth - offset);

    };

    Slick.prototype.setFade = function() {

        var _ = this,
            targetLeft;

        _.$slides.each(function(index, element) {
            targetLeft = (_.slideWidth * index) * -1;
            if (_.options.rtl === true) {
                $(element).css({
                    position: 'relative',
                    right: targetLeft,
                    top: 0,
                    zIndex: _.options.zIndex - 2,
                    opacity: 0
                });
            } else {
                $(element).css({
                    position: 'relative',
                    left: targetLeft,
                    top: 0,
                    zIndex: _.options.zIndex - 2,
                    opacity: 0
                });
            }
        });

        _.$slides.eq(_.currentSlide).css({
            zIndex: _.options.zIndex - 1,
            opacity: 1
        });

    };

    Slick.prototype.setHeight = function() {

        var _ = this;

        if (_.options.slidesToShow === 1 && _.options.adaptiveHeight === true && _.options.vertical === false) {
            var targetHeight = _.$slides.eq(_.currentSlide).outerHeight(true);
            _.$list.css('height', targetHeight);
        }

    };

    Slick.prototype.setOption =
    Slick.prototype.slickSetOption = function() {

        /**
         * accepts arguments in format of:
         *
         *  - for changing a single option's value:
         *     .slick("setOption", option, value, refresh )
         *
         *  - for changing a set of responsive options:
         *     .slick("setOption", 'responsive', [{}, ...], refresh )
         *
         *  - for updating multiple values at once (not responsive)
         *     .slick("setOption", { 'option': value, ... }, refresh )
         */

        var _ = this, l, item, option, value, refresh = false, type;

        if( $.type( arguments[0] ) === 'object' ) {

            option =  arguments[0];
            refresh = arguments[1];
            type = 'multiple';

        } else if ( $.type( arguments[0] ) === 'string' ) {

            option =  arguments[0];
            value = arguments[1];
            refresh = arguments[2];

            if ( arguments[0] === 'responsive' && $.type( arguments[1] ) === 'array' ) {

                type = 'responsive';

            } else if ( typeof arguments[1] !== 'undefined' ) {

                type = 'single';

            }

        }

        if ( type === 'single' ) {

            _.options[option] = value;


        } else if ( type === 'multiple' ) {

            $.each( option , function( opt, val ) {

                _.options[opt] = val;

            });


        } else if ( type === 'responsive' ) {

            for ( item in value ) {

                if( $.type( _.options.responsive ) !== 'array' ) {

                    _.options.responsive = [ value[item] ];

                } else {

                    l = _.options.responsive.length-1;

                    // loop through the responsive object and splice out duplicates.
                    while( l >= 0 ) {

                        if( _.options.responsive[l].breakpoint === value[item].breakpoint ) {

                            _.options.responsive.splice(l,1);

                        }

                        l--;

                    }

                    _.options.responsive.push( value[item] );

                }

            }

        }

        if ( refresh ) {

            _.unload();
            _.reinit();

        }

    };

    Slick.prototype.setPosition = function() {

        var _ = this;

        _.setDimensions();

        _.setHeight();

        if (_.options.fade === false) {
            _.setCSS(_.getLeft(_.currentSlide));
        } else {
            _.setFade();
        }

        _.$slider.trigger('setPosition', [_]);

    };

    Slick.prototype.setProps = function() {

        var _ = this,
            bodyStyle = document.body.style;

        _.positionProp = _.options.vertical === true ? 'top' : 'left';

        if (_.positionProp === 'top') {
            _.$slider.addClass('slick-vertical');
        } else {
            _.$slider.removeClass('slick-vertical');
        }

        if (bodyStyle.WebkitTransition !== undefined ||
            bodyStyle.MozTransition !== undefined ||
            bodyStyle.msTransition !== undefined) {
            if (_.options.useCSS === true) {
                _.cssTransitions = true;
            }
        }

        if ( _.options.fade ) {
            if ( typeof _.options.zIndex === 'number' ) {
                if( _.options.zIndex < 3 ) {
                    _.options.zIndex = 3;
                }
            } else {
                _.options.zIndex = _.defaults.zIndex;
            }
        }

        if (bodyStyle.OTransform !== undefined) {
            _.animType = 'OTransform';
            _.transformType = '-o-transform';
            _.transitionType = 'OTransition';
            if (bodyStyle.perspectiveProperty === undefined && bodyStyle.webkitPerspective === undefined) _.animType = false;
        }
        if (bodyStyle.MozTransform !== undefined) {
            _.animType = 'MozTransform';
            _.transformType = '-moz-transform';
            _.transitionType = 'MozTransition';
            if (bodyStyle.perspectiveProperty === undefined && bodyStyle.MozPerspective === undefined) _.animType = false;
        }
        if (bodyStyle.webkitTransform !== undefined) {
            _.animType = 'webkitTransform';
            _.transformType = '-webkit-transform';
            _.transitionType = 'webkitTransition';
            if (bodyStyle.perspectiveProperty === undefined && bodyStyle.webkitPerspective === undefined) _.animType = false;
        }
        if (bodyStyle.msTransform !== undefined) {
            _.animType = 'msTransform';
            _.transformType = '-ms-transform';
            _.transitionType = 'msTransition';
            if (bodyStyle.msTransform === undefined) _.animType = false;
        }
        if (bodyStyle.transform !== undefined && _.animType !== false) {
            _.animType = 'transform';
            _.transformType = 'transform';
            _.transitionType = 'transition';
        }
        _.transformsEnabled = _.options.useTransform && (_.animType !== null && _.animType !== false);
    };


    Slick.prototype.setSlideClasses = function(index) {

        var _ = this,
            centerOffset, allSlides, indexOffset, remainder;

        allSlides = _.$slider
            .find('.slick-slide')
            .removeClass('slick-active slick-center slick-current')
            .attr('aria-hidden', 'true');

        _.$slides
            .eq(index)
            .addClass('slick-current');

        if (_.options.centerMode === true) {

            centerOffset = Math.floor(_.options.slidesToShow / 2);

            if (_.options.infinite === true) {

                if (index >= centerOffset && index <= (_.slideCount - 1) - centerOffset) {

                    _.$slides
                        .slice(index - centerOffset, index + centerOffset + 1)
                        .addClass('slick-active')
                        .attr('aria-hidden', 'false');

                } else {

                    indexOffset = _.options.slidesToShow + index;
                    allSlides
                        .slice(indexOffset - centerOffset + 1, indexOffset + centerOffset + 2)
                        .addClass('slick-active')
                        .attr('aria-hidden', 'false');

                }

                if (index === 0) {

                    allSlides
                        .eq(allSlides.length - 1 - _.options.slidesToShow)
                        .addClass('slick-center');

                } else if (index === _.slideCount - 1) {

                    allSlides
                        .eq(_.options.slidesToShow)
                        .addClass('slick-center');

                }

            }

            _.$slides
                .eq(index)
                .addClass('slick-center');

        } else {

            if (index >= 0 && index <= (_.slideCount - _.options.slidesToShow)) {

                _.$slides
                    .slice(index, index + _.options.slidesToShow)
                    .addClass('slick-active')
                    .attr('aria-hidden', 'false');

            } else if (allSlides.length <= _.options.slidesToShow) {

                allSlides
                    .addClass('slick-active')
                    .attr('aria-hidden', 'false');

            } else {

                remainder = _.slideCount % _.options.slidesToShow;
                indexOffset = _.options.infinite === true ? _.options.slidesToShow + index : index;

                if (_.options.slidesToShow == _.options.slidesToScroll && (_.slideCount - index) < _.options.slidesToShow) {

                    allSlides
                        .slice(indexOffset - (_.options.slidesToShow - remainder), indexOffset + remainder)
                        .addClass('slick-active')
                        .attr('aria-hidden', 'false');

                } else {

                    allSlides
                        .slice(indexOffset, indexOffset + _.options.slidesToShow)
                        .addClass('slick-active')
                        .attr('aria-hidden', 'false');

                }

            }

        }

        if (_.options.lazyLoad === 'ondemand') {
            _.lazyLoad();
        }

    };

    Slick.prototype.setupInfinite = function() {

        var _ = this,
            i, slideIndex, infiniteCount;

        if (_.options.fade === true) {
            _.options.centerMode = false;
        }

        if (_.options.infinite === true && _.options.fade === false) {

            slideIndex = null;

            if (_.slideCount > _.options.slidesToShow) {

                if (_.options.centerMode === true) {
                    infiniteCount = _.options.slidesToShow + 1;
                } else {
                    infiniteCount = _.options.slidesToShow;
                }

                for (i = _.slideCount; i > (_.slideCount -
                        infiniteCount); i -= 1) {
                    slideIndex = i - 1;
                    $(_.$slides[slideIndex]).clone(true).attr('id', '')
                        .attr('data-slick-index', slideIndex - _.slideCount)
                        .prependTo(_.$slideTrack).addClass('slick-cloned');
                }
                for (i = 0; i < infiniteCount; i += 1) {
                    slideIndex = i;
                    $(_.$slides[slideIndex]).clone(true).attr('id', '')
                        .attr('data-slick-index', slideIndex + _.slideCount)
                        .appendTo(_.$slideTrack).addClass('slick-cloned');
                }
                _.$slideTrack.find('.slick-cloned').find('[id]').each(function() {
                    $(this).attr('id', '');
                });

            }

        }

    };

    Slick.prototype.interrupt = function( toggle ) {

        var _ = this;

        if( !toggle ) {
            _.autoPlay();
        }
        _.interrupted = toggle;

    };

    Slick.prototype.selectHandler = function(event) {

        var _ = this;

        var targetElement =
            $(event.target).is('.slick-slide') ?
                $(event.target) :
                $(event.target).parents('.slick-slide');

        var index = parseInt(targetElement.attr('data-slick-index'));

        if (!index) index = 0;

        if (_.slideCount <= _.options.slidesToShow) {

            _.setSlideClasses(index);
            _.asNavFor(index);
            return;

        }

        _.slideHandler(index);

    };

    Slick.prototype.slideHandler = function(index, sync, dontAnimate) {

        var targetSlide, animSlide, oldSlide, slideLeft, targetLeft = null,
            _ = this, navTarget;

        sync = sync || false;

        if (_.animating === true && _.options.waitForAnimate === true) {
            return;
        }

        if (_.options.fade === true && _.currentSlide === index) {
            return;
        }

        if (_.slideCount <= _.options.slidesToShow) {
            return;
        }

        if (sync === false) {
            _.asNavFor(index);
        }

        targetSlide = index;
        targetLeft = _.getLeft(targetSlide);
        slideLeft = _.getLeft(_.currentSlide);

        _.currentLeft = _.swipeLeft === null ? slideLeft : _.swipeLeft;

        if (_.options.infinite === false && _.options.centerMode === false && (index < 0 || index > _.getDotCount() * _.options.slidesToScroll)) {
            if (_.options.fade === false) {
                targetSlide = _.currentSlide;
                if (dontAnimate !== true) {
                    _.animateSlide(slideLeft, function() {
                        _.postSlide(targetSlide);
                    });
                } else {
                    _.postSlide(targetSlide);
                }
            }
            return;
        } else if (_.options.infinite === false && _.options.centerMode === true && (index < 0 || index > (_.slideCount - _.options.slidesToScroll))) {
            if (_.options.fade === false) {
                targetSlide = _.currentSlide;
                if (dontAnimate !== true) {
                    _.animateSlide(slideLeft, function() {
                        _.postSlide(targetSlide);
                    });
                } else {
                    _.postSlide(targetSlide);
                }
            }
            return;
        }

        if ( _.options.autoplay ) {
            clearInterval(_.autoPlayTimer);
        }

        if (targetSlide < 0) {
            if (_.slideCount % _.options.slidesToScroll !== 0) {
                animSlide = _.slideCount - (_.slideCount % _.options.slidesToScroll);
            } else {
                animSlide = _.slideCount + targetSlide;
            }
        } else if (targetSlide >= _.slideCount) {
            if (_.slideCount % _.options.slidesToScroll !== 0) {
                animSlide = 0;
            } else {
                animSlide = targetSlide - _.slideCount;
            }
        } else {
            animSlide = targetSlide;
        }

        _.animating = true;

        _.$slider.trigger('beforeChange', [_, _.currentSlide, animSlide]);

        oldSlide = _.currentSlide;
        _.currentSlide = animSlide;

        _.setSlideClasses(_.currentSlide);

        if ( _.options.asNavFor ) {

            navTarget = _.getNavTarget();
            navTarget = navTarget.slick('getSlick');

            if ( navTarget.slideCount <= navTarget.options.slidesToShow ) {
                navTarget.setSlideClasses(_.currentSlide);
            }

        }

        _.updateDots();
        _.updateArrows();

        if (_.options.fade === true) {
            if (dontAnimate !== true) {

                _.fadeSlideOut(oldSlide);

                _.fadeSlide(animSlide, function() {
                    _.postSlide(animSlide);
                });

            } else {
                _.postSlide(animSlide);
            }
            _.animateHeight();
            return;
        }

        if (dontAnimate !== true) {
            _.animateSlide(targetLeft, function() {
                _.postSlide(animSlide);
            });
        } else {
            _.postSlide(animSlide);
        }

    };

    Slick.prototype.startLoad = function() {

        var _ = this;

        if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {

            _.$prevArrow.hide();
            _.$nextArrow.hide();

        }

        if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

            _.$dots.hide();

        }

        _.$slider.addClass('slick-loading');

    };

    Slick.prototype.swipeDirection = function() {

        var xDist, yDist, r, swipeAngle, _ = this;

        xDist = _.touchObject.startX - _.touchObject.curX;
        yDist = _.touchObject.startY - _.touchObject.curY;
        r = Math.atan2(yDist, xDist);

        swipeAngle = Math.round(r * 180 / Math.PI);
        if (swipeAngle < 0) {
            swipeAngle = 360 - Math.abs(swipeAngle);
        }

        if ((swipeAngle <= 45) && (swipeAngle >= 0)) {
            return (_.options.rtl === false ? 'left' : 'right');
        }
        if ((swipeAngle <= 360) && (swipeAngle >= 315)) {
            return (_.options.rtl === false ? 'left' : 'right');
        }
        if ((swipeAngle >= 135) && (swipeAngle <= 225)) {
            return (_.options.rtl === false ? 'right' : 'left');
        }
        if (_.options.verticalSwiping === true) {
            if ((swipeAngle >= 35) && (swipeAngle <= 135)) {
                return 'down';
            } else {
                return 'up';
            }
        }

        return 'vertical';

    };

    Slick.prototype.swipeEnd = function(event) {

        var _ = this,
            slideCount,
            direction;

        _.dragging = false;
        _.interrupted = false;
        _.shouldClick = ( _.touchObject.swipeLength > 10 ) ? false : true;

        if ( _.touchObject.curX === undefined ) {
            return false;
        }

        if ( _.touchObject.edgeHit === true ) {
            _.$slider.trigger('edge', [_, _.swipeDirection() ]);
        }

        if ( _.touchObject.swipeLength >= _.touchObject.minSwipe ) {

            direction = _.swipeDirection();

            switch ( direction ) {

                case 'left':
                case 'down':

                    slideCount =
                        _.options.swipeToSlide ?
                            _.checkNavigable( _.currentSlide + _.getSlideCount() ) :
                            _.currentSlide + _.getSlideCount();

                    _.currentDirection = 0;

                    break;

                case 'right':
                case 'up':

                    slideCount =
                        _.options.swipeToSlide ?
                            _.checkNavigable( _.currentSlide - _.getSlideCount() ) :
                            _.currentSlide - _.getSlideCount();

                    _.currentDirection = 1;

                    break;

                default:


            }

            if( direction != 'vertical' ) {

                _.slideHandler( slideCount );
                _.touchObject = {};
                _.$slider.trigger('swipe', [_, direction ]);

            }

        } else {

            if ( _.touchObject.startX !== _.touchObject.curX ) {

                _.slideHandler( _.currentSlide );
                _.touchObject = {};

            }

        }

    };

    Slick.prototype.swipeHandler = function(event) {

        var _ = this;

        if ((_.options.swipe === false) || ('ontouchend' in document && _.options.swipe === false)) {
            return;
        } else if (_.options.draggable === false && event.type.indexOf('mouse') !== -1) {
            return;
        }

        _.touchObject.fingerCount = event.originalEvent && event.originalEvent.touches !== undefined ?
            event.originalEvent.touches.length : 1;

        _.touchObject.minSwipe = _.listWidth / _.options
            .touchThreshold;

        if (_.options.verticalSwiping === true) {
            _.touchObject.minSwipe = _.listHeight / _.options
                .touchThreshold;
        }

        switch (event.data.action) {

            case 'start':
                _.swipeStart(event);
                break;

            case 'move':
                _.swipeMove(event);
                break;

            case 'end':
                _.swipeEnd(event);
                break;

        }

    };

    Slick.prototype.swipeMove = function(event) {

        var _ = this,
            edgeWasHit = false,
            curLeft, swipeDirection, swipeLength, positionOffset, touches;

        touches = event.originalEvent !== undefined ? event.originalEvent.touches : null;

        if (!_.dragging || touches && touches.length !== 1) {
            return false;
        }

        curLeft = _.getLeft(_.currentSlide);

        _.touchObject.curX = touches !== undefined ? touches[0].pageX : event.clientX;
        _.touchObject.curY = touches !== undefined ? touches[0].pageY : event.clientY;

        _.touchObject.swipeLength = Math.round(Math.sqrt(
            Math.pow(_.touchObject.curX - _.touchObject.startX, 2)));

        if (_.options.verticalSwiping === true) {
            _.touchObject.swipeLength = Math.round(Math.sqrt(
                Math.pow(_.touchObject.curY - _.touchObject.startY, 2)));
        }

        swipeDirection = _.swipeDirection();

        if (swipeDirection === 'vertical') {
            return;
        }

        if (event.originalEvent !== undefined && _.touchObject.swipeLength > 4) {
            event.preventDefault();
        }

        positionOffset = (_.options.rtl === false ? 1 : -1) * (_.touchObject.curX > _.touchObject.startX ? 1 : -1);
        if (_.options.verticalSwiping === true) {
            positionOffset = _.touchObject.curY > _.touchObject.startY ? 1 : -1;
        }


        swipeLength = _.touchObject.swipeLength;

        _.touchObject.edgeHit = false;

        if (_.options.infinite === false) {
            if ((_.currentSlide === 0 && swipeDirection === 'right') || (_.currentSlide >= _.getDotCount() && swipeDirection === 'left')) {
                swipeLength = _.touchObject.swipeLength * _.options.edgeFriction;
                _.touchObject.edgeHit = true;
            }
        }

        if (_.options.vertical === false) {
            _.swipeLeft = curLeft + swipeLength * positionOffset;
        } else {
            _.swipeLeft = curLeft + (swipeLength * (_.$list.height() / _.listWidth)) * positionOffset;
        }
        if (_.options.verticalSwiping === true) {
            _.swipeLeft = curLeft + swipeLength * positionOffset;
        }

        if (_.options.fade === true || _.options.touchMove === false) {
            return false;
        }

        if (_.animating === true) {
            _.swipeLeft = null;
            return false;
        }

        _.setCSS(_.swipeLeft);

    };

    Slick.prototype.swipeStart = function(event) {

        var _ = this,
            touches;

        _.interrupted = true;

        if (_.touchObject.fingerCount !== 1 || _.slideCount <= _.options.slidesToShow) {
            _.touchObject = {};
            return false;
        }

        if (event.originalEvent !== undefined && event.originalEvent.touches !== undefined) {
            touches = event.originalEvent.touches[0];
        }

        _.touchObject.startX = _.touchObject.curX = touches !== undefined ? touches.pageX : event.clientX;
        _.touchObject.startY = _.touchObject.curY = touches !== undefined ? touches.pageY : event.clientY;

        _.dragging = true;

    };

    Slick.prototype.unfilterSlides = Slick.prototype.slickUnfilter = function() {

        var _ = this;

        if (_.$slidesCache !== null) {

            _.unload();

            _.$slideTrack.children(this.options.slide).detach();

            _.$slidesCache.appendTo(_.$slideTrack);

            _.reinit();

        }

    };

    Slick.prototype.unload = function() {

        var _ = this;

        $('.slick-cloned', _.$slider).remove();

        if (_.$dots) {
            _.$dots.remove();
        }

        if (_.$prevArrow && _.htmlExpr.test(_.options.prevArrow)) {
            _.$prevArrow.remove();
        }

        if (_.$nextArrow && _.htmlExpr.test(_.options.nextArrow)) {
            _.$nextArrow.remove();
        }

        _.$slides
            .removeClass('slick-slide slick-active slick-visible slick-current')
            .attr('aria-hidden', 'true')
            .css('width', '');

    };

    Slick.prototype.unslick = function(fromBreakpoint) {

        var _ = this;
        _.$slider.trigger('unslick', [_, fromBreakpoint]);
        _.destroy();

    };

    Slick.prototype.updateArrows = function() {

        var _ = this,
            centerOffset;

        centerOffset = Math.floor(_.options.slidesToShow / 2);

        if ( _.options.arrows === true &&
            _.slideCount > _.options.slidesToShow &&
            !_.options.infinite ) {

            _.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');
            _.$nextArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

            if (_.currentSlide === 0) {

                _.$prevArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
                _.$nextArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

            } else if (_.currentSlide >= _.slideCount - _.options.slidesToShow && _.options.centerMode === false) {

                _.$nextArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
                _.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

            } else if (_.currentSlide >= _.slideCount - 1 && _.options.centerMode === true) {

                _.$nextArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
                _.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

            }

        }

    };

    Slick.prototype.updateDots = function() {

        var _ = this;

        if (_.$dots !== null) {

            _.$dots
                .find('li')
                .removeClass('slick-active')
                .attr('aria-hidden', 'true');

            _.$dots
                .find('li')
                .eq(Math.floor(_.currentSlide / _.options.slidesToScroll))
                .addClass('slick-active')
                .attr('aria-hidden', 'false');

        }

    };

    Slick.prototype.visibility = function() {

        var _ = this;

        if ( _.options.autoplay ) {

            if ( document[_.hidden] ) {

                _.interrupted = true;

            } else {

                _.interrupted = false;

            }

        }

    };

    $.fn.slick = function() {
        var _ = this,
            opt = arguments[0],
            args = Array.prototype.slice.call(arguments, 1),
            l = _.length,
            i,
            ret;
        for (i = 0; i < l; i++) {
            if (typeof opt == 'object' || typeof opt == 'undefined')
                _[i].slick = new Slick(_[i], opt);
            else
                ret = _[i].slick[opt].apply(_[i].slick, args);
            if (typeof ret != 'undefined') return ret;
        }
        return _;
    };

}));

$(function(){

    // hide FAQ first element for mobile
    if(document.body.clientWidth <= 769){
        //$('#ca1').collapse('hide');
    }

    // selects
    $(document).on('click','.new-select span',function(){
        var text = $(this).text();
        var val = $(this).attr('data-val');
        $(this).closest('.new-select').find('b').text(text);
        $(this).closest('.new-select').find('b').attr('data-val',val);
        $(this).closest('div').hide();
    });

    $('.new-select i').on('click',function(){
        $('.new-select div').not($(this).closest('.new-select').find('div')).hide();
        $(this).closest('.new-select').find('div').toggle();
    });


});

setTimeout(update_img_and_bg,1);

// menu hover on desktop
if($(window).width() > 769){
    $('header ul li').on('mouseover',function(e){
        $(this).find('ul').show();
        width = $(this).find('a').width();
        $(this).find('.menu-cursor').css('transform','translateX('+ (width/2-7) +'px) rotate(45deg)');

    }).on('mouseout',function(e){

        $(this).find('ul').hide();
        $(this).find('.menu-cursor').remove();
    });
}


// вызов живо-сайта
$('#o_k_click button').click(function(){
    (function(){ var widget_id = 'Y5Z2sXofKC';var d=document;var w=window;function l(){
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
        s.src = '//code.jivosite.com/script/widget/'+widget_id
        ; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}
        if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}
        else{w.addEventListener('load',l,false);}}})();
});


// защита от копирования
document.oncopy = function () {
    var bodyElement = document.body;
    var selection = getSelection();
    var href = document.location.href;
    if(window.isAuth == undefined){
        var copyright = ' - Читайте подробнее на #ВЗО: <a href="'+ href +'">' + href + '</a>';
    } else {
        var copyright = '';
    }
    var text = selection + copyright;
    var divElement = document.createElement('div');
    divElement.style.position = 'absolute';
    divElement.style.left = '-99999px';
    text1 = document.createTextNode(text); //создал текстовый узел
    divElement.appendChild(text1); //и добавил его
    bodyElement.appendChild(divElement);
    selection.selectAllChildren(divElement);
    setTimeout(function(){
        bodyElement.removeChild(divElement);
    }, 0);
};

function update_img_and_bg(){
    $('img.loading_lazy').each(function () {
        $(this).wrap('<div class="single-img-wrap"></div>');
    });


    $('.def_bg[data-src]').each(function(){
        $(this).css('background','url('+$(this).attr('data-src')+')').removeAttr('data-src');
    })
}

function update_img_and_bg_full_version(){

    $('.def_bg[data-src]').each(function(){
        $(this).css('background','url('+$(this).attr('data-src')+')').removeAttr('data-src');
    })
}




$('.hint').on('click',function(){
  $('.form-hint').not($(this).next('p.form-hint')).hide();
  $(this).next('p.form-hint').toggle();
});





$(document).on('scroll',function(){
  var block = $('.fixed-blue');
  if(block.css('display')=='none'){
    offsetTop = $(window).scrollTop();
    if(offsetTop > 100){
      block.show();
      $('body').css('padding-top',block.outerHeight());
    }
  }
})


$('#menu-mob-button').on('click',function(){
    $('.mob-menu-wrap').show();
    $('.mob-menu-wrap').animate({
        'left':'0'
    },700,function(){});
});

$('.mob-close').on('click',function(){
    $('.mob-menu-wrap').animate({
        'left':'-200%'
    },700,function(){
        $('.mob-menu-wrap').hide();
    });
});

$('ul.mobl-menu li a.a-sub-m').click(function(e){
    e.preventDefault();
    $('#sub-menu').find('div').html('<i class="fa fa-arrow-left"><span>' + $(this).attr('data-text') + '</span></i>');
    $('#sub-menu').find('ul').html($(this).parent().find('ul').html());
    $('#sub-menu').animate({
        'left':'0'
    },700,function(){});
});

$('#sub-menu-title').click(function(){
    $('#sub-menu').animate({
        'left':'-200%'
    },700,function(){});
});



$('.side-block-dart .side-title').on('click',function(){
  $(this).next('.side-box').toggle();
  if($(this).find('i').hasClass("fa-angle-up")){
            $(this).find('i').removeClass("fa-angle-up").addClass("fa-angle-down");
        } else {
            $(this).find('i').addClass("fa-angle-up").removeClass("fa-angle-down");
        }
});



var files;

// Вешаем функцию на событие
// Получим данные файлов и добавим их в переменную
$('input[type=file]').change(function(event){
    files = event.target.files;
});

window.company_add_submited = false;
$('#company_add').on('submit',function(){

    if(window.company_add_submited == true) {
        $('#formModal .modal-body').html('Вы уже оправили сообщение');
        $('#formModal').modal();
        return false;
    };

    //var token = $('meta[name="csrf-token"]').attr('content');
    /*
    var name = $('#name').val();
    var email = $('#email').val();
    var comment = $('#comment').val();
    var captcha = $('#g-recaptcha-response').val();
    */
    var files_;



    var formData = new FormData();
    formData.append('name', $('#name').val());
    formData.append('email', $('#email').val());
    formData.append('comment', $('#comment').val());
    formData.append('captcha', $('#g-recaptcha-response').val());

    var i =1;
    $.each( files, function( key, value ){
        formData.append('file'+i, value);
        i++;
        //files_.append( key, value );
    });
/*
    var data_files = new FormData();

    */


    $.ajax({
        type: "POST",
        url: "/forms/company_add",
        data: formData,
        processData: false,
        contentType: false,
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        /*
        data: {
            //'auth' : false,
            '_token': token,
            'name': name,
            'email': email,
            'comment': comment,
            'captcha':captcha,
            'files': data_files
        },
        */
        //dataType: "json",
        success: function(data){
            $('#formModal .modal-body').html('<p>'+data+'</p>');
            $('#formModal').modal();
            window.company_add_submited = true;
        }
        });
    return false;
});





$('#widget_install').on('submit',function(){

    var token = $('meta[name="csrf-token"]').attr('content');
    var name = $('#name').val();
    var email = $('#email').val();
    var company = $('#company').val();
    var comment = $('#comment').val();
    var captcha = $('#g-recaptcha-response').val();

    $.ajax({
type: "POST",
url: "/forms/widget_install",
data: {
    //'auth' : false,
    '_token': token,
    'name': name,
    'email': email,
    'company':company,
    'comment': comment,
    'captcha':captcha
},
//dataType: "json",
success: function(data){
    $('#formModal .modal-body').html('<p>'+data+'</p>');
    $('#formModal').modal();
}
});
    return false;
});





$('#support').on('submit',function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    var name = $('#name').val();
    var email = $('#email').val();
    var comment = $('#comment').val();
    var captcha = $('#g-recaptcha-response').val();
    $.ajax({
        type: "POST",
        url: "/forms/support",
        data: {
            '_token': token,
            'name': name,
            'email': email,
            'comment': comment,
            'captcha':captcha
        },
        success: function(data){
            $('#formModal .modal-body').html('<p>'+data+'</p>');
            $('#formModal').modal();
        }
    });
    return false;
});




$('#form_advertising').on('submit',function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    var name = $('#name').val();
    var phone = $('#phone').val();
    var email = $('#email').val();
    var company = $('#company').val();
    var question = $('#question').val();
    var captcha = $('#g-recaptcha-response').val();
    $.ajax({
        type: "POST",
        url: "/forms/advertising",
        data: {
            '_token': token,
            'name': name,
            'phone': phone,
            'email': email,
            'company': company,
            'question': question,
            'captcha':captcha
        },
        success: function(data){
            $('#formModal .modal-body').html('<p>'+data+'</p>');
            $('#formModal').modal();
            $('#name').val('');
            $('#phone').val('');
            $('#email').val('');
            $('#company').val('');
            $('#question').val('');
        }
    });
    return false;
});



$('#form_zalogi').on('submit',function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    var name = $('#name').val();
    var phone = $('#phone').val();
    var page = location.href;
    $.ajax({
        type: "POST",
        url: "/forms/zalogi",
        data: {
            '_token': token,
            'name': name,
            'phone': phone,
            'page': page,
        },
        success: function(data){
            $('#formModal .modal-body').html('<p>'+data+'</p>');
            $('#formModal').modal();
            $('#name').val('');
            $('#phone').val('');
        }
    });
    return false;
});



$('#form_rko').on('submit',function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var phone = $("#phone").val();
    var region = $("#region").val();
    var register_form = $("#register_form").val();
    var taxation_system = $("#taxation_system").val();
    var term_of_business = $("#term_of_business").val();
    var sum = $("#sum").val();
    var payment_count = $("#payment_count").val();
    var additional_services = $("#additional_services").val();
    $.ajax({
        type: "POST",
        url: "/forms/rko",
        data: {
            '_token': token,
            'first_name': first_name,
            'last_name': last_name,
            'phone': phone,
            'region': region,
            'register_form': register_form,
            'taxation_system': taxation_system,
            'term_of_business': term_of_business,
            'sum': sum,
            'payment_count': payment_count,
            'additional_services': additional_services,
        },
        success: function(data){
            $('#formModal .modal-body').html('<p>'+data+'</p>');
            $('#formModal').modal();
            $('#first_name').val('');
            $('#last_name').val('');
            $('#phone').val('');
            $('#region').val('');
            $('#sum').val('');
            $('#payment_count').val('');
        }
    });
    return false;
});










$('.showPhoneForm0').on('click',function(){
    $('#creditHistory').show();
    $('html, body').animate({
        scrollTop: $("#creditHistory").offset().top
    }, 2000);
});

$('#creditHistory').on('submit',function(){
    alert('Сервис временно недоступен');
    return false;
});





















/* акардион */
$('.itop').each(function(){
    if($(this).parent().find('.imore').css('display') == 'block')
        $(this).prepend('<i class="fa fa-minus"></i>');
    else
        $(this).prepend('<i class="fa fa-plus"></i>');
});

$('.itop').on('click',function(){
    if($(this).find('i').hasClass('fa-plus')){
        $(this).find('i').addClass('fa-minus').removeClass('fa-plus');
    } else {
        $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
    }
    $(this).next('.imore').toggle();
    $(this).toggleClass('active-itop');
    $('.itop').not($(this)).removeClass('active-itop');
});

$('.next-accordion').on('click',function(){
    $(this).closest('.imore').hide();
    $(this).closest('.hitem').find('.itop').find('i').addClass('fa-plus').removeClass('fa-minus');
    $(this).closest('.hitem').next().find('.imore').show();
    $(this).closest('.hitem').next().find('.itop').find('i').removeClass('fa-minus').addClass('fa-minus');
    $([document.documentElement, document.body]).animate({
        scrollTop: $(this).closest('.hitem').next().find('.itop').offset().top
    }, 2000);
});


















$(function(){
    $('.post-ratings i').each(function(){
        $(this).attr('data-value',$(this).attr('class'));
    })
});



$('.post-ratings i').on('mouseover',function(){
    var item = $(this).attr('data-item');
    for(var i=1; i<=item; i++){
        $(this).parent().find('i[data-item="'+i+'"]').attr('class','fa fa-star star-hover');
    }
}).on('mouseout',function(){
        var parent = $(this).parent();
        parent.find('i').each(function(){
            $(this).attr('class','fa '+$(this).attr('data-value'));
        });
}).on('click',function(){
    var rating = $(this).attr('data-item');
    var type = $(this).parent().attr('data-type');
    var id = $(this).parent().attr('data-id');
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: "/forms/rating_add",
        data: {
            '_token': token,
            'rating': rating,
            'id': id,
            'type' : type,
        },
        success: function(data){
            $('.post-ratings').html(data);
        }
    });
    return false;
});





$('.companies-rating i').on('mouseover',function(){
    var item = $(this).attr('data-item');
    for(var i=1; i<=item; i++){
        $(this).parent().find('i[data-item="'+i+'"]').attr('class','fa fa-star star-hover');
    }
}).on('mouseout',function(){
    if($('#reviewRating').val() == 0){
        var parent = $(this).parent();
        parent.find('i').each(function(){
            $(this).attr('class','fa '+$(this).attr('data-value'));
        });
    } else {
        $(this).parent().find('i').attr('class','fa fa-star-o');
        for(var i=1; i<=$('#reviewRating').val(); i++){
            $(this).parent().find('i[data-item="'+i+'"]').attr('class','fa fa-star');
        }
    }
}).on('click',function(){
    $(this).parent().find('i').attr('class','fa fa-star-o');
    var value = $(this).attr('data-item');
    $('#reviewRating').val(value);

    $(this).parent().find('i').attr('class','fa fa-star-o');
    for(var i=1; i<=$('#reviewRating').val(); i++){
        $(this).parent().find('i[data-item="'+i+'"]').attr('class','fa fa-star');
    }
});







$('.insert_video').on('click', function(){
    var parent = $(this).closest('.insert-video-wrap');
    var video = parent.find('.data-video').attr('data-video');
    var html = '<div class="iframe-shadow"><iframe width="560" height="315" src="'+ video +'"></iframe></div>';
    parent.html(html);

});

$('.video-button').on('click', function(){
    var parent = $(this).closest('.insert-video-wrap');
    var video = parent.find('.data-video').attr('data-video');
    var html = '<div class="iframe-shadow"><iframe width="560" height="315" src="'+ video +'"></iframe></div>';
    parent.html(html);
});









$(document).on('click','.cart_more',function(){
    $(this).next('.panel-cart').toggle();
    if($(this).find('i').hasClass('fa-angle-down')){
        $(this).find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
    } else {
        $(this).find('i').addClass('fa-angle-down').removeClass('fa-angle-up');
    }
})


                
$(window).scroll(function() {
    if($(this).scrollTop() != 0) {
        $('#toTop').fadeIn();
    } else {
        $('#toTop').fadeOut();
    }
});    
 
$('#toTop').click(function() {
    $('body,html').animate({scrollTop:0},800);
});
























// скрол на странице продуктов

if(document.body.clientWidth > 768){
if ($(".fixed-company")[0]){
$(document).ready(function($) {
    $nav = $('.fixed-company');
    //$nav.css('width', $nav.outerWidth());
    $window = $(window);
    $h = $nav.offset().top;
    $h += 50;
    $window.scroll(function() {
        if ($window.scrollTop() > $h) {
            $nav.addClass('fixed');
        } else {
            $nav.removeClass('fixed');
        }
    });
});
}

}

function getLoadCountReview(){
    var current = parseInt($('#loadReviews').attr('data-groups-current'));
    var selector = '.rev-group-' + (++current);
    var next_count = $(selector).length;
    $('#loadReviews span').html(' '+next_count);
}

$(function(){
    getLoadCountReview();
});


$('#loadReviews').on('click',function(){
    var current = parseInt($(this).attr('data-groups-current'));
    var all = parseInt($(this).attr('data-groups-count'));
    var selector = '.rev-group-' + (++current);

    $(selector).removeClass('display_none');
    if(all <= (current)){
        $(this).remove();
    } else {
        $(this).attr('data-groups-current',(current));
    }
    getLoadCountReview();
});




// переключение вкладов в компаниях
$('.vab .left-block li').on('click',function(){
    var id = $(this).attr('data-id');
    var vab = $(this).closest('.vab');
    $('.left-block li').not(this).removeClass('active');
    $(this).addClass('active');
    vab.find('.right-block>div').removeClass('show');
    vab.find('.right-block>div[data-id='+id+']').addClass('show');
});


$('.remove-review').on('click',function(){
    var id = $(this).closest('.comment-item').attr('id');
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: "/actions/remove-review",
        data: {
            '_token': token,
            'id': id,
        }
    });
    $(this).closest('.comment-item').remove();
    return false;
});


$(document).on('click','.edit-review',function(e){
    var id = $(this).closest('.comment-item').attr('id');
    id = id.replace('comment-','');
    var token = $('meta[name="csrf-token"]').attr('content');
    var rating = prompt('Введите значение нового рейтинга');
    rating = rating.replace(',','.');
    rating = parseFloat(rating);

    var values = [0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5];
    if(values.indexOf(rating) == -1)
    {
        alert('Введенное значение не является числом или не подходит для поля "Рейтинг"');
        return false;
    }


    $.ajax({
        type: "POST",
        url: "/actions/edit-review",
        data: {
            '_token': token,
            'id': id,
            'rating': rating
        }
    });
    return false;
});

// печать карточки
$(document).on('click','.print_card',function(e){
    e.preventDefault();

   var sOption="toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,width=900,height=300,left=100,top=25";
   var sWinHTML = $(this).closest("div.one-offer").html();

   var winprint=window.open("","",sOption);
       winprint.document.open();
       winprint.document.write('<html><head><link href="/vzo_theme/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet"><link href="/vzo_theme/css/bootstrap.min.css" rel="stylesheet"><link href="/vzo_theme/css/s.php?v=0853" rel="stylesheet">');
       winprint.document.write('<style>.no-print{display:none !important}.row.spidometrs>div,.row.three-block>div{display:inline-block;width:33%}* { -webkit-print-color-adjust: exact; }</style></head><body onload="window.print();">');
       winprint.document.write('<div class="offers-list"><div class="one-offer">');
       winprint.document.write(sWinHTML);
       winprint.document.write('</div></div>');
       winprint.document.write('</body></html>');
       winprint.document.close();
       winprint.focus();
});


// мы помогли
$(document).on('click','.hdl',function(e){
    $.ajax({
        type: "GET",
        url: "/actions/inc-help-count",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'link': $(this).attr('href'),
            'card_id': $(this).attr('data-id'),
            'city': window.city,
            'client_id': window.clientID
        },
    });
    var current = $('.side-box .help span').text();
    current = current.replace(' ','');
    current = parseInt(current);
    ++current;
    current = current.toString();
    current = current.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    $('.side-box .help span').text(current);
});



// избранные карточки
$(function(){
    favorites();
    compare();
});

function favorites(){
    //favorites = localStorage.setItem('vzo',Array(1,2,3));
    favorites = localStorage.getItem('vzo');
    if(favorites == null) return;
    favoritesArr = favorites.split(',');
    for(i=0; i<favoritesArr.length; i++){
      if(favoritesArr[i] == '') favoritesArr.splice(i, 1);
    }
    localStorage.setItem('vzo',favoritesArr)
    var count = favoritesArr.length;
    $('.go-to-favorites').show();
    $('.go-to-favorites').append(' <span>'+count+'</span>');
    $('.one-offer').each(function(){
        var fav = $(this).find('.favorite');
        var id = fav.attr('data-id');
        for(i=0; i<favoritesArr.length; i++){
            if(parseInt(id) == parseInt(favoritesArr[i])){
                $(fav).removeClass('add_to_favorite').addClass('remove_from_favorite').find('span.fav').text('Удалить из избранных');
                if (window.isAuth == true) {
                    $(fav).after('<br class="go_to_fav"><a class="go_to_fav" href="/account/favorites"><i class="fa fa-sign-in"></i> Перейти в избранное</a>');
                } else {
                    $(fav).after('<br class="go_to_fav"><a class="go_to_fav" href="/favorites"><i class="fa fa-sign-in"></i> Перейти в избранное</a>');
                }
            }
        }
    });
    //localStorage.removeItem("vzo");
}

$(document).on('click','.add_to_favorite',function(e){
    e.preventDefault();
    favorites = localStorage.getItem('vzo');
    if(favorites == null){
        favoritesArr = Array();
    } else {
        favoritesArr = favorites.split(',');
    }
    favoritesArr.push($(this).attr('data-id'));
    favorites = localStorage.setItem('vzo',favoritesArr);
    $(this).removeClass('add_to_favorite').addClass('remove_from_favorite').find('span.fav').text('Удалить из избранных');
    if (window.isAuth == true) {
        $(this).after('<br class="go_to_fav"><a class="go_to_fav" href="/account/favorites"><i class="fa fa-sign-in"></i> Перейти в избранное</a>');
    } else {
        $(this).after('<br class="go_to_fav"><a class="go_to_fav" href="/favorites"><i class="fa fa-sign-in"></i> Перейти в избранное</a>');
    }

});

$(document).on('click','.remove_from_favorite',function(e){
    e.preventDefault();
    favorites = localStorage.getItem('vzo');
    if(favorites == null){
        favoritesArr = Array();
    } else {
        favoritesArr = favorites.split(',');
    }
    var id = $(this).attr('data-id');
    for(i=0;i<favoritesArr.length; i++){
        if(parseInt(favoritesArr[i]) == parseInt(id)){
            favoritesArr.splice(i, 1);
        }
    }

    favorites = localStorage.setItem('vzo',favoritesArr);
    $(this).addClass('add_to_favorite').removeClass('remove_from_favorite').find('span.fav').text('В избранное');
    $(this).closest('.one-offer').find('.go_to_fav').remove();
});

/******************************************************************************************************/
/***************************************** СРАВНЕНИЕ КАРТОЧЕК *****************************************/
/******************************************************************************************************/
function compare(){

    var link = '';
    switch (window.category_id){
        case 1: link = '/compare'; break;
        case 2: link = '/rko/compare'; break;
        case 4: link = '/online-credit/compare'; break;
        case 5: link = '/credit-cards/compare'; break;
        case 6: link = '/debit-cards/compare'; break;
        default: link =  '/compare';
    }
    var buttons = '';
    buttons = buttons + '<a href="'+link+'" class="form-btn1"><b class="fa fa-angle-double-left"></b> Перейти к сравнению</a>';
    buttons = buttons + '<button id="compare-clear" data-id="'+window.category_id+'">Сбросить</a>';
    $('.cilp_inner').html(buttons);

    favorites = localStorage.getItem('vzo_compare'+window.category_id);

    if(favorites == null) return;

    favoritesArr = favorites.split(',');
    for(i=0; i<favoritesArr.length; i++){
        if(favoritesArr[i] == '') favoritesArr.splice(i, 1);
    }
    localStorage.setItem('vzo_compare'+window.category_id,favoritesArr);
    $('.one-offer').each(function(){
        var fav = $(this).find('.compare');
        var id = fav.attr('data-id');
        for(i=0; i<favoritesArr.length; i++){
            if(parseInt(id) == parseInt(favoritesArr[i])){
                $(fav).removeClass('add_to_compare').addClass('remove_from_compare').find('span').text('- удалить из сравнения');
                $(fav).after('<a class="go_to_compare" href="'+link+'"><i class="fa fa-sign-in"></i> Перейти к сравнению</a>');
            }
        }
    });
/*
    if(document.body.clientWidth > 768) {
        $.ajax({
            type: "POST",
            url: "/compare_load_images",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'cards': favorites,
                'id': window.category_id
            },
            success: function (data) {
                $('.cilp_inner').html(data+buttons);
                $('#compare_in_listing_pages').show();
            }
        });
    } else {
        $('.cilp_inner').html(buttons);
    }
*/

    //localStorage.removeItem("vzo_compare");
}
// end function compare

// добавление к сравнению
$(document).on('click','.add_to_compare',function(e){
    e.preventDefault();
    favorites = localStorage.getItem('vzo_compare'+window.category_id);
    if(favorites == null){
        favoritesArr = Array();
    } else {
        favoritesArr = favorites.split(',');
        if(favoritesArr.length == 10){
            alert('Нельзя добавлять более 10 карточек одного раздела в сравнение');
            return;
        }
    }
    var id = $(this).attr('data-id');
    favoritesArr.push(id);
    var logo = $(this).closest('.one-offer').find('.bor img').attr('src');
    var item = '<span data-id="'+id+'"><img src="'+logo+'" alt=""><i class="fa fa-remove"></i></span>';
    var link = '';
    switch (window.category_id){
        case 1: link = '/compare'; break;
        case 2: link = '/rko/compare'; break;
        case 4: link = '/online-credit/compare'; break;
        case 5: link = '/credit-cards/compare'; break;
        case 6: link = '/debit-cards/compare'; break;
        default: link =  '/compare';
    }
    $('.cilp_inner').prepend(item);
    $('#compare_in_listing_pages').show();
    favorites = localStorage.setItem('vzo_compare'+window.category_id,favoritesArr);
    $(this).removeClass('add_to_compare').addClass('remove_from_compare').find('span').text('- удалить из избранных');
    $(this).after('<a class="go_to_compare" href="'+link+'"><i class="fa fa-sign-in"></i> Перейти к сравнению</a>');
});

// удаление из сравнения (из карточки)
$(document).on('click','.remove_from_compare',function(e){
    e.preventDefault();
    favorites = localStorage.getItem('vzo_compare'+window.category_id);
    if(favorites == null){
        favoritesArr = Array();
    } else {
        favoritesArr = favorites.split(',');
    }
    var id = $(this).attr('data-id');
    for(i=0;i<favoritesArr.length; i++){
        if(parseInt(favoritesArr[i]) == parseInt(id)){
            favoritesArr.splice(i, 1);
        }
    }
    favorites = localStorage.setItem('vzo_compare'+window.category_id,favoritesArr);
    $(this).addClass('add_to_compare').removeClass('remove_from_compare').find('span').text('+ к сравнению');
    $(this).closest('.one-offer').find('.go_to_compare').remove();
    $('.cilp_inner span').each(function(){
        if (parseInt($(this).attr('data-id')) == parseInt(id)) $(this).remove();
    });
});

// удаление из сравнения (из плашки)
$(document).on('click','.cilp_inner i',function() {
    favorites = localStorage.getItem('vzo_compare' + window.category_id);
    var span = $(this).parent();
    if (favorites == null) {
        favoritesArr = Array();
    } else {
        favoritesArr = favorites.split(',');
    }
    var id = $(this).parent('span').attr('data-id');
    for (i = 0; i < favoritesArr.length; i++) {
        if (parseInt(favoritesArr[i]) == parseInt(id)) {
            favoritesArr.splice(i, 1);
            //console.log(parseInt(id));
        }
    }
    favorites = localStorage.setItem('vzo_compare' + window.category_id, favoritesArr);
    span.remove();
});

// очистка сравнения
$(document).on('click','#compare-clear',function(){
    localStorage.removeItem("vzo_compare"+window.category_id);
    document.location.reload();
});

$(document).on('click','.col-md-4a i.fa-remove',function(){
    var id = $(this).attr('data-id');
    favorites = localStorage.getItem('vzo_compare' + window.category_id);
    for (i = 0; i < favoritesArr.length; i++) {
        if (parseInt(favoritesArr[i]) == parseInt(id)) {
            favoritesArr.splice(i, 1);
        }
    }
    favorites = localStorage.setItem('vzo_compare' + window.category_id, favoritesArr);
    document.location.reload();
});

/******************************************************************************************************/
/******************************************************************************************************/





// закрытие блоков между карточками
$('.info-offer i.fa-close').on('click',function(){
    $(this).closest('.info-offer').remove();
});









// подписка юнисендер

var yourForm=document.getElementById('subscription_form');

yourForm.addEventListener('submit',function(e){
    "use strict";
    e.preventDefault();
    var email = $('#subscription_email').val();

    if (email== ''){
        alert('Укажите email');
        return false;
    }

    //var name = $('#subscription_name').val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: "/actions/unisender",
        data: {
            '_token': token,
            'email': email,
            //'name': name,
        }
    });
    var html = '<div class="text-center"><i class="fa fa-check-circle"></i><br><p>Спасибо за подписку';
    html += '<br>Письмо с подтверждением отправлено вам на почту.</p></div>';
    $('.form_unisender').html(html);
    return false;
});

// калькулятор микрозаймов
$('.calc-block button').on('click',function(){
    var sum = $('#mc_summ').val();
    var days = $('#mc_term').val();
    var percent = $('#mc_percent').val().replace(',', '.');
    var category_id = $('#mc_term').attr('data-category-id');

    if (category_id == 7) {
        var total = sum * days * percent / 700;
    } else {
        var total = sum * percent * 0.01 * days;
    }


    if (isNaN(total)) {
        $('.mc_result').html('Переплата: <span>0</span>');
    } else {
        total = total.toFixed(2);
        total = total.replace(/(\d)(?=(\d{3})+(\D|$))/g, '$1 ');
        total = total.toString().replace('.', ',');
        $('.mc_result').html('Переплата:<br> <span>'+total+' <i class="fa fa-rouble"></i></span>');
    }
});

// сортировка карточек
$('.sorting-line span').on('click',function(){
    $('#load_more').show();
    var field = $(this).parent().attr('data-field');
    window.field = field;
    window.number_page = 1;
    var token = $('meta[name="csrf-token"]').attr('content');
    var options = Array();

    $('.options-list span').each(function(){
        if($(this).find('input:checked')) options.push ($(this).find('input:checked').val()); 
    });

    if($(this).parent().hasClass('active')){
        if($(this).parent().find('i').hasClass('fa-arrow-circle-up')){
            $(this).parent().find('i').removeClass('fa-arrow-circle-up').addClass('fa-arrow-circle-down');
            var sort_type = 'desc';
        } else {
            var sort_type = 'asc';
            $(this).parent().find('i').addClass('fa-arrow-circle-up').removeClass('fa-arrow-circle-down');
        }
        window.sort_type = sort_type;


        window.default_sorting_counter = 0;
        
    } else {
        $('.sorting-line li').each(function(){
            $(this).removeClass('active');
            $(this).find('i').attr('class','');
        });
        $(this).parent().find('i').addClass('fa-arrow-circle-down').addClass('fa')
        $(this).parent().addClass('active');

    }

    if (field == 'title' || field == 'maintenance') {
        if (sort_type == 'asc') {
            sort_type = 'desc';
        } else {
            sort_type = 'asc';
        }
    }

    if (sort_type == undefined) {
        sort_type = 'desc';
    }

    window.default_sorting_counter++;

    var data = {};
    data['field'] = field;
    data['page'] = 1;
    data['listing_id'] = window.listing_id;
    data['category_id'] = window.category_id;
    data['count_on_page'] = window.count_on_page;
    data['options'] = options;
    data['sort_type'] = sort_type;
    data['section_type'] = window.section_type;


    for(var key in window.sidebar_listings) {
        data[key] = window.sidebar_listings[key];
    }
	
	//alert(JSON.stringify(data));return false;


    $.ajax({
        type: "GET",
        url: "/actions/load_cards_for_listings",
        data: data,
        success: function(data){
            $('.offers-list').html(data['code']);

            update_img_and_bg_full_version();

            if(data['count']){
                $('#load_more').show();
                $('#load_more_index_page').show();
            } else {
                $('#load_more').hide();
                $('#load_more_index_page').hide();
            }

            if (data['count'] < 10) {
                $('#load_more').hide();
            }

            countTemp = window.cards_count - window.number_page*10;

            if(countTemp > 10)
                labelCount = 10;
            else
                labelCount = countTemp;

            $("#load_more").find('span').html(labelCount);


        }
    });
});



// подгрузка карточек
$('#load_more').on('click',function(){
    var bnt = $(this);
    window.number_page++;
    var token = $('meta[name="csrf-token"]').attr('content');
    var options = Array();

    $('.options-list span').each(function(){
        if($(this).find('input:checked')) options.push ($(this).find('input:checked').val()); 
    });

    var data = {};
    data['field'] =  window.field;
    data['page'] = window.number_page;
    data['listing_id'] = window.listing_id;
    data['category_id'] = window.category_id;
    data['count_on_page'] = window.count_on_page;
    data['options'] = options;
    data['sort_type'] = window.sort_type;
    data['section_type'] = window.section_type;




    for(var key in window.sidebar_listings) {
        data[key] = window.sidebar_listings[key];
    }
	
    
    $.ajax({
        type: "GET",
        url: '/actions/load_cards_for_listings',
        data: data,
        success: function(data){
            $('.offers-list').append(data['code']);

            update_img_and_bg_full_version();

            if(data['count']){
                $('#load_more').show();
                //$('#load_more_index_page').show();
            } else {
                $('#load_more').hide();
                //$('#load_more_index_page').hide();
            }

            if (data['count'] < 10) {
                $('#load_more').hide();
            }

            countTemp = window.cards_count - window.number_page*10;

            if(countTemp > 10)
                labelCount = 10;
            else
                labelCount = countTemp;


            if (labelCount < 0) {
                if(window.cards_count > 20)
                    labelCount = 10;
                else
                    labelCount = window.cards_count - 10;
            }

            if (labelCount == 0) {
                $('#load_more').hide();
            }


            bnt.find('span').html(labelCount);





        }
    });
});






// forms
$('#creditRating').on('submit',function(e){
    //e.preventDefault();
    if($('#last_name').val()=='') { $('#last_name').focus(); return false; }
    if($('#first_name').val()=='') { $('#first_name').focus(); return false; }
    if($('#middle_name').val()=='') { $('#middle_name').focus(); return false; }
    if($('#passport').val()=='') { $('#passport').focus(); return false; }
    if($('#birthday').val()=='') { $('#birthday').focus(); return false; }
    if($('#passport_date').val()=='') { $('#passport_date').focus(); return false; }
    if($('#email').val()=='') { $('#email').focus(); return false; }
    return true;
});

$('.go-to-page').on('click',function(){
    if($('b.active-element').attr('data-val').indexOf('autocredit') > -1) {
        location.href = '/' + $('b.active-element').attr('data-val');
    } else {
        location.href = $('b.active-element').attr('data-val');
    }
});
$('.go-to-page2').on('click',function(){
    location.href =  $('b.active-element2').attr('data-val');
});

if(document.body.clientWidth < 768){
$('.name-line').each(function(){
  if($(this).text().length <= 10){
    $(this).closest('.one-offer').find('.mob-mar').css('margin-top','95px');
  } else {
    if($(this).text().length >=29){
      $(this).closest('.one-offer').find('.mob-mar').css('margin-top','145px');
    } else {

    }
  }

});
}

$(document).on('click','.card_remove',function(){
    var card = $(this);
    var id = $(this).attr('data-id');
    var ccid = $(this).attr('data-ccid');
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        data: {'_token': token,card_id:id,listing_id:listing_id,'ccid':ccid},
        url: '/actions/remove-card',
        success: function (data) {
            card.closest('.one-offer').remove();
        }
    });

});


// promo
$('.getPromo').on('click',function(){
    var input = $(this).closest('.center-block').find('input');
    input.attr('value',input.attr('data-value'));
});

//comparison
if(document.body.clientWidth > 768){
$(function(){
    $('.cmp_wrap').each(function(){
        var cmp_wrap = $(this);
        var max = 0;
        cmp_wrap.find('.cmp_i').each(function(){
            if($(this).height() > max ) max = $(this).height(); 
        });
        cmp_wrap.find('.cmp_i').each(function(){
            $(this).height(max);
        });
    });
});
}


$(document).on('submit','#callMeForm_',function(e){
  //$('#callMeForm').submit(function(e){
      e.preventDefault();
      var token = $('meta[name="csrf-token"]').attr('content');
      var name = $('#c_name').val();
      var phone = $('#c_hone').val();

      if (!name || !phone) {
          alert('Вы не заполнили все поля');
          return false;
      }


      $.ajax({
          type: "POST",
          url: "/forms/call_me",
          data: {
              '_token': token,
              'name': name,
              'phone': phone
          },
          success: function(data){
            $('#callMe .modal-body').html('<p>'+data+'</p>');
          }
      });

      return false;
});



$("#searchInputBySite").bind("change paste keyup", function() {

    if($(this).val().length > 2){
        var token = $('meta[name="csrf-token"]').attr('content');
        var value = $(this).val();
        $.ajax({
            type: "GET",
            url: "/forms/search_hint",
            data: {
                '_token': token,
                's': value
            },
            success: function(data){
                if(data.length>0){
                    var res = '';
                    for(i=0; i<data.length; i++){
                        if(data[i]!= null) res = res + "<li>" + data[i] + "</li>";
                    }
                    $('#search-hint').html(res);
                    $('#search-hint').show('block');
                }
            }
        });
    } else {
        $('#search-hint').hide();
        $('#search-hint').html('');
    }
});

$(document).on('click','#search-hint li',function(){
    $('#searchInputBySite').val($(this).text());
    $('.search-wrap-form form').submit();
});


$('.zero-pos-more').on('click',function(e){
    e.preventDefault()
    if($(this).find('i').hasClass('fa-plus')){
        $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
        $('.zero-pos').toggle();
    } else {
        $(this).find('i').addClass('fa-plus').removeClass('fa-minus');
        $('.zero-pos').toggle();
    }
});



var join = $('.search-form'),
    joinLink = $('.header-search'),
    indexClick = 0;
$ ( function() {
    joinLink.click( function(event) {
        if (indexClick === 0) {
            join.fadeIn(700);
            join.show()
            indexClick = 1;
            joinLink.addClass('fa-remove').removeClass('.header-search');
        }
        else {
            join.hide();
            indexClick = 0;
            joinLink.removeClass('fa-remove').addClass('.header-search');
        }
        event.stopPropagation();
    });
});
$(document).click(function(event) {
    if ($(event.target).closest(".search-form").length) return;
    join.hide();
    indexClick = 0;
    joinLink.removeClass('fa-remove').addClass('.header-search');
    event.stopPropagation();
});




$(document).on('click','.form-hint-img',function(){
    $(this).closest('.form-group').find('p').toggle();
});



// подсказки у карточек у займов оплата и погашение
$(document).on('mouseover touchstart','.zaim-p-icon',function(){
    $('.sprite-block').remove();
    title = $(this).attr('data-title');
    $(this).append('<div class="sprite-block">'+title+'</div>');
}).on('mouseout','.zaim-p-icon',function(){
    $(this).parent().find('.sprite-block').remove();
});


// новые подсказки (глобальные)
$(document).on('mouseover touchstart','.vzo_icons',function(){
    $('.sprite-block').remove();
    title = $(this).attr('data-title');
    $(this).append('<div class="sprite-block">'+title+'</div>');
}).on('mouseout','.vzo_icons',function(){
    $(this).parent().find('.sprite-block').remove();
});

// этот код хз откуда и надо ли
$(document).on('mouseout','.pay-icons',function(){
    $(this).parent().find('.sprite-block').remove();
});

// подказки на шаблоне компаний.. нужно будет убрать после переписания
$(document).on('mouseover touchstart','.zaym_cards span',function(){
    $('.sprite-block').remove();
    id = $(this).attr('data-icon');
    title = '';
    switch (id){
        case    "1" : title = "Без процентов"; break;
        case    "2" : title = "С плохой КИ"; break;
        case    "3" : title = "Круглосуточно"; break;
        case    "4" : title = "С продлением"; break;
        case    "5" : title = "Моментальные"; break;
        case    "6" : title = "Погашение по частям"; break;
        case    "7" : title = "Микрофинансовая компания"; break;
        case    "8" : title = "Микрокредитная компания"; break;
    }
    $(this).append('<div class="sprite-block">'+title+'</div>');
});










// раскрытие отзывов
$('.show_the_reviews').click(function(){
    $(this).next().show();
    $(this).prev('.three_dots').remove();
    $(this).remove();
});

// активные ссылки
$(function(){

    checkMenu('.desktop > li > a');
    checkMenu('#menu-verhnee-menyu-new > li > a');

    function checkMenu(selector){
        $(selector).each(function(){
            var isChildMenu = false;
            if($(this).attr('href') == location.pathname ){
                $(this).removeAttr('href').addClass('active');
            } else{
                $(this).parent().find('ul li').each(function () {
                    if($(this).find('a').attr('href') == location.pathname){
                        $(this).find('a').removeAttr('href').addClass('active');
                        isChildMenu = true;
                    }
                });
            }
            if(isChildMenu){
                $(this).addClass('active');
            }
        });
    }

});

// скрол на хабах

if((document.body.clientWidth > 1200) && ($('body').find('.ltable').length > 0)){
    $('.ltable th').each(function(){
        width = $(this).width();
       $(this).attr('width',width);
    });
    $(document).ready(function($) {
        var foot = $(".ltable thead").clone().html();
        $('.ltable').append('<tfoot style="height: 10px;">' + foot + '</tfoot>');
        $nav = $('.ltable');
        $window = $(window);
        $h = $nav.offset().top -30;
        $window.scroll(function() {

            //console.log($window.scrollTop() > $h);
            if ($window.scrollTop() > $h) {
                $nav.find('thead').addClass('f-block');
            } else {
                $nav.find('thead').removeClass('f-block');
            }

            if($window.scrollTop() > $h + $nav.height()){
                $nav.find('thead').removeClass('f-block');
            }

        });
    });
}

$('.show_all_filtres').on('click',function(){
   $(this).hide();
   $('.all_filttres_wrap').show();
});



$(document).ready(function () {

    if(document.body.clientWidth > 768){


        $('.single img').each(function(){
            if($(this).attr('width') > 300){
                $(this).wrap('<div class="single-img-wrap"></div>');
            }
        });
        $('.single-page img').each(function(){
            if($(this).attr('width') > 300){
                $(this).wrap('<div class="single-img-wrap"></div>');
            }
        });
        $('#single_content_wrap img').each(function(){
            if($(this).attr('width') > 300){
                $(this).wrap('<div class="single-img-wrap"></div>');
            }
        });
        $('.children-pages img').each(function(){
            if($(this).attr('width') > 300){
                $(this).wrap('<div class="single-img-wrap"></div>');
            }
        });

    }


    $('.single iframe:not(.none)').wrap('<div class="iframe-shadow"></div>');
    //$('.single-page iframe:not(.none)').wrap('<div class="iframe-shadow"></div>');
    $('#single_content_wrap iframe:not(.none)').wrap('<div class="iframe-shadow"></div>');
    //$('.children-pages iframe:not(.none)').wrap('<div class="iframe-shadow"></div>');


    //$('#single_content_wrap iframe:not(.none)').wrap('<div class="iframe-shadow-2"></div>');
});


// жалоба
$(document).on('click','.complaint', function () {
    window.complaint_card_id = $(this).attr('data-id');
    $('#CardComplaint').modal();
});

$('#CardComplaintSelect .line').on('click',function () {
    if ($(this).attr('data-val') == 9) {
        $('#CardComplaintText').show();
    } else {
        $('#CardComplaintText').hide();
    }
});

$('#CardComplaint button.form-btn1').on('click',function() {

    var token = $('meta[name="csrf-token"]').attr('content');
    var type = $('#CardComplaintSelect .active-element').attr('data-val');
    var message = $('#CardComplaintText').val();

    if (type == 0) {
        alert('Вы не выбрали пункт');
        return false;
    }
    if (type == 9 && message == '') {
        alert('Вы не заполнили поле');
    }


    $.ajax({
        type: "POST",
        url: "/actions/add-complaint",
        data: {
            '_token': token,
            'type': type,
            'message': message,
            'card_id': window.complaint_card_id,
            'metrika_id':  window.clientID
        },
        success: function(data){
            //$('#CardComplaint .modal-body').html('<p>'+data+'</p>');
            alert(data);
            $('#CardComplaint').modal('toggle');
        }
    });
    return false;

});
// конец жалоба

$(function (){
    if($(".g-recaptcha").length) {
        dynamicallyLoadScript('https://www.google.com/recaptcha/api.js');
    }
});

// tabs from bootstrap
$(function(){
    $('.nav-tabs a').on('click',function(e){
        e.preventDefault();
        id = $(this).attr('href');
        $('.nav-tabs a').removeClass('active');
        $(this).addClass('active');
        $(this).closest('.nav-tabs-wrap').find('.tab-content>div').css('display','none');
        $(this).closest('.nav-tabs-wrap').find('.tab-content').find(id).css('display','block');
    });
})

// только числа
function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

$(function(){
   //$('#reger').text(document.body.clientWidth) ;

    // нажатие Esc
    $(document).keyup(function(e) {
        if(e.key === "Escape") {
            $('.modal').removeClass('show');
        }
    });

});



// фиксация меню в подвале

if((document.body.clientWidth > 1200) && ($('body').find('.sidebar_menu_wrap').length > 0)){

    $(document).ready(function($) {

        var section = $('h2'), // теги - заголовки, к которым вяжутся якоря
            nav = $('.side_bar_menu_scroll'),
            navHeight = nav.outerHeight(); // получаем высоту навигации

        // поворот экрана
        window.addEventListener('orientationchange', function () {
            navHeight = nav.outerHeight();
        }, false);




        $('.sidebar_menu_wrap .side-block').css('width',$('.sidebar').width());

        $h = $('.sidebar').offset().top
            + $('.sidebar').height()
            - $('.sidebar .sidebar_menu_wrap').height();

        // определение какой класс вешать
        if ($('body').find('.fixed-company').length > 0) {
            class_name = 'sidebar-fixed-block-on-company';
        } else {
            class_name = 'sidebar-fixed-block';
        }


        $(window).scroll(function() {

            // фиксация самого меню в сайдбаре при скролле
            if ($(window).scrollTop() > $h) {
                $('.main').find('.sidebar_menu_wrap .side-block').addClass(class_name);
            } else {
                $('.main').find('.sidebar_menu_wrap .side-block').removeClass(class_name);
            }

            const_offset = 500;
            /*
            if($(".fixed-company").length) {
                const_offset = 800;
            }
            */

            if($(window).scrollTop() > $('.main').height() - const_offset){
                $('.main').find('.sidebar_menu_wrap .side-block').removeClass(class_name);
            }




            var position = $(this).scrollTop();
            // выделение активной ссылки в меню при скролле
            section.each(function () {
                var top = $(this).offset().top - navHeight - 5,
                    bottom = top + $(this).outerHeight();


                if (position >= top && position <= bottom) {
                    nav.find('a').removeClass('active');
                    section.removeClass('active');

                    $(this).addClass('active');
                    console.log($(this));
                    nav.find('a[href="#' + $(this).attr('id') + '"]').addClass('active');
                }
            });


        });

        // плавный скролл
        nav.find('a').on('click', function () {
            var id = $(this).attr('href');

            $('html, body').animate({
                scrollTop: $(id).offset().top - navHeight
            }, 500);

            return false;
        });


    });
}

$('.show_more_db').click(function () {
    $('.ic-db-wrap li').removeClass('d_n');
    $(this).hide();
});


var initialPoint;
var finalPoint;
document.addEventListener('touchstart', function(event) {
    initialPoint=event.changedTouches[0];
}, false);
document.addEventListener('touchend', function(event) {
    finalPoint=event.changedTouches[0];
    var xAbs = Math.abs(initialPoint.pageX - finalPoint.pageX);
    var yAbs = Math.abs(initialPoint.pageY - finalPoint.pageY);
    if (xAbs > 150 || yAbs > 150) {
        if (xAbs > yAbs) {
            if (finalPoint.pageX < initialPoint.pageX){
                $('.mob-menu-wrap').animate({
                    'left':'-200%'
                },700,function(){
                    $('.mob-menu-wrap').hide();
                });
            }
            else{
                $('.mob-menu-wrap').show();
                $('.mob-menu-wrap').animate({
                    'left':'0'
                },700,function(){});
            }}
        else {
            if (finalPoint.pageY < initialPoint.pageY){
                /*СВАЙП ВВЕРХ*/
            }
            else{
                /*СВАЙП ВНИЗ*/
            }}}}, false);


$('.ep_btn').on('click', function(){
    $(this).parent().removeClass('ep_text_wrap');
   $(this).remove();
});

$('.verified_by_expert').on('click', function (e) {
    var menu_height = 150;
    var target = $(this).attr('href');
    e.preventDefault();
    $('html, body').animate({
        scrollTop: $(target).offset().top - menu_height
    }, 1000);
});



var switcher = document.getElementById("switcher");
if(switcher){
switcher.addEventListener("click", function () {
    if (switcher.className.match("sun")) {
        document.cookie = "DARK_MODE=dark; path=/;";
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = '/vzo_theme/css/dark_mode.css';
        link.media = 'all';
        link.id = 'dark_styles';
        head.appendChild(link);
        switcher.className = switcher.className.replace("sun", "moon");
    } else {
        $('#dark_styles').remove();
       // $(document).on('click', '#dark_styles', function(){
         //   alert(1);
            //$(this).remove();
        //});
        document.cookie = "DARK_MODE=dark; path=/; Max-Age=-1";
        switcher.className = switcher.className.replace("moon", "sun");
    }
});
}


// мини плагин
(function(){
    $.fn.modal = function(){
        $(this).addClass('show');
        $("body").css({
            "overflow": "hidden"
        });
    }
})($);

$(function(){
    // закрытие
    $(document).on('click','.modal .close', function () {
        $(this).closest('.modal').removeClass('show');
        $("body").css({
            "overflow": "auto"
        });
        $('#formModal .modal-body').html('');
    });
    // вызыв
    $(document).on('click','[data-toggle=modal]', function () {
        var selector = $(this).attr('data-target');
        $(selector).addClass('show');
        $("body").css({
            "overflow": "hidden"
        });
    });

});



if ($(window).height() < 650) {
    $('.geo_cities_list').height('180px');
}

$(document).on('click', '.modal.show', function(e){
     if ($(e.target).hasClass('modal')) {
         $(this).removeClass('show');
         $('body').css('overflow', 'initial')
     }
    //console.log ($(e.target).attr('class'));
});


