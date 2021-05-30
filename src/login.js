window.addEventListener('load', () => {

	setTimeout(function() {
		const $redirect = document.getElementById('cas-login-redirect')
		if ($redirect) {
			if ($redirect instanceof HTMLAnchorElement) {
				window.location.href = $redirect.href
			} else if ($redirect instanceof HTMLFormElement) {
				$redirect.submit()
			}
		}
	}, 500)

})
