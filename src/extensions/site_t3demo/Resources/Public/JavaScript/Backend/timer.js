const timerSpan = document.getElementById('b13-t3demo-timer');

// Avoid errors if the element does not exist, e.g. in Development context
if (timerSpan) {
  const endTime = timerSpan.getAttribute('data-endtime');

  const updateText = (minutesRemaining) => {
    if (minutesRemaining < 1) {
      timerSpan.innerHTML = 'Next reset in less than 1 minute';
    } else {
      timerSpan.innerHTML = `Next reset in ${minutesRemaining} minutes`;
    }
  };

  // If we don't set the timer before the interval the time remaining will only be shown after 10 seconds.
  const minutesRemaining = Math.floor((endTime - Math.floor(Date.now() / 1000)) / 60);
  updateText(minutesRemaining);

  const timeInterval = setInterval(() => {
    // endTime is in UNIX seconds, while Date.now() is in milliseconds,
    // so we need to convert it to whole seconds first.
    // Then we can subtract them and divide by 60 to get the minutes left.
    const minutesRemaining = Math.floor((endTime - Math.floor(Date.now() / 1000)) / 60);

    updateText(minutesRemaining);

    if (minutesRemaining <= 0) {
      clearInterval(timeInterval);
    }
  }, 10000);
}

export default {};
