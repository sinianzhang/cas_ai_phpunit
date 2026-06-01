/**
 * navigation
 */
class Navigation {
  constructor() {
    this.s = {
      navtrigger: 'navtrigger',
      resetbannerTrigger: '.bJS_resetbanner-trigger',
    };
    this.resetbanner = document.querySelector(this.s.resetbannerTrigger)

    this.events();
  }

  events() {
    const navtrigger = document.getElementById(this.s.navtrigger);
    navtrigger.addEventListener('change', () => {
      this.toggleNavigation();
    })
  }

  /**
   * toggle navigation
   */
  toggleNavigation() {
    if (document.getElementById(this.s.navtrigger).checked === true) {
      this.disableScrolling();
      this.resetbanner.style.visibility = 'hidden';
    } else {
      this.enableScrolling();
      this.resetbanner.style.visibility = 'visible';
    }
  }

  /**
   * disable scrolling
   */
  disableScrolling() {
    document.documentElement.classList.add('b_navi--open');
  }

  /**
   * enable scrolling
   */
  enableScrolling() {
    document.documentElement.classList.remove('b_navi--open');
  }
}

export default Navigation;
