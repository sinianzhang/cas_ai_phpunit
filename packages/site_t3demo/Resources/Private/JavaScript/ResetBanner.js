/**
 * Reset Banner
 */
class ResetBanner {

	constructor() {
		this.events();
		const resetTime = this.getResetTime();
		resetTime.then(({ resetTime }) => {
			this._resetTime = resetTime;
			this.update();

			const timeInterval = setInterval(() => {
				this.update();

				if (this._minutesRemaining <= 0) {
					clearInterval(timeInterval);
				}
			}, 10000);

			// open reset banner if current user has no internal referrer
			const referrer = document.referrer;
			const internal = referrer.indexOf(window.location.hostname);
			if (internal === -1) {
				this.openResetBanner();
			}
		});
	}

	/**
	 * events
	 */
	events() {
		const toggleBanner = document.getElementById('resetbanner');
		toggleBanner.addEventListener('change', () => {
			this.toggleResetBanner();
		})
	}

	/**
	 * update
	 *
	 * update minutes remaining and text
	 */
	update() {
		// endTime is in UNIX seconds, while Date.now() is in milliseconds,
		// so we need to convert it to whole seconds first.
		// Then we can subtract them and divide by 60 to get the minutes left.
		this._minutesRemaining = Math.floor(
			(this._resetTime - Math.floor(Date.now() / 1000)) /60
		);
		this.updateTimerText();
	}

	/**
	 * update timer text
	 */
	updateTimerText() {
		const timerSpan = document.getElementById('b13-t3demo-timer');

		if (this._minutesRemaining <= 0) {
			timerSpan.innerHtml = '';
			document.getElementById('b13-t3demo-timerwrap').classList.remove('b_resetbanner__timer--visible');
			document.getElementById('b13-t3demo-timerplaceholder').classList.add('b_resetbanner__placeholder--visible');
		} else if (this._minutesRemaining < 1) {
			timerSpan.innerHTML = '> 1';
			document.getElementById('b13-t3demo-timerwrap').classList.add('b_resetbanner__timer--visible');
		} else {
			timerSpan.innerHTML = `${this._minutesRemaining}`;
			document.getElementById('b13-t3demo-timerwrap').classList.add('b_resetbanner__timer--visible');
		}
	}

	/**
	 * toggle reset banner
	 */
	toggleResetBanner() {
		if (document.getElementById('resetbanner').checked === true) {
			this.openResetBanner();
		} else {
			this.closeResetBanner();
		}
	}

	/**
	 * open reset banner
	 */
	openResetBanner() {
		document.getElementById('resetbanner').checked = true;
		document.documentElement.classList.add('b_resetbanner--open');
	}

	/**
	 * close reset banner
	 */
	closeResetBanner() {
		document.getElementById('resetbanner').checked = false;
		document.documentElement.classList.remove('b_resetbanner--open');
	}

	/**
	 * get reset time
	 * @returns {Promise<any>}
	 */
	async getResetTime() {
		const response = await fetch('/resettimer.html');

		if (response.status === 200) {
			return await response.json();
		}

		throw new Error(response);
	}
}

export default ResetBanner;
