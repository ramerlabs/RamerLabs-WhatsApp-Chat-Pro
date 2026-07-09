(function () {
	'use strict';

	function getConfig(instanceId) {
		if (instanceId && window['rlwcWidget_' + instanceId]) {
			return window['rlwcWidget_' + instanceId];
		}
		return typeof rlwcWidget !== 'undefined' ? rlwcWidget : null;
	}

	function getSessionId(cfg) {
		try {
			var key = (cfg && cfg.sessionKey) || 'rlwc_session';
			var existing = window.localStorage.getItem(key);
			if (existing) {
				return existing;
			}
			var id = 'rlwc_' + Math.random().toString(36).slice(2) + Date.now().toString(36);
			window.localStorage.setItem(key, id);
			return id;
		} catch (e) {
			return 'rlwc_guest';
		}
	}

	function trackClick(cfg, consent) {
		if (!cfg) {
			return;
		}
		var payload = {
			session_id: getSessionId(cfg),
			page_url: cfg.pageUrl,
			page_title: cfg.pageTitle,
			agent_id: cfg.agentId,
			department: cfg.department,
			rule_id: cfg.ruleId,
			consent: !!consent
		};

		if (cfg.utm) {
			payload.utm_source = cfg.utm.utm_source || '';
			payload.utm_medium = cfg.utm.utm_medium || '';
			payload.utm_campaign = cfg.utm.utm_campaign || '';
		}

		fetch(cfg.restUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': cfg.nonce
			},
			body: JSON.stringify(payload),
			keepalive: true
		}).catch(function () {});
	}

	function bindInstance(root, cfg) {
		if (!cfg || !root) {
			return;
		}

		var button = root.querySelector('[data-rlwc-trigger]') || root.querySelector('#rlwc-chat-button') || root.querySelector('.rlwc-inline__button');
		var instanceId = cfg.instanceId || (root.getAttribute('data-rlwc-instance') || 'floating');
		var modal = document.getElementById('rlwc-gdpr-modal-' + instanceId) || root.querySelector('.rlwc-widget__modal');
		var confirmBtn = modal ? modal.querySelector('.rlwc-widget__modal-confirm') : null;
		var titleEl = modal ? modal.querySelector('.rlwc-widget__modal-title') : null;
		var textEl = modal ? modal.querySelector('.rlwc-widget__modal-text') : null;
		var privacyEl = modal ? modal.querySelector('.rlwc-widget__modal-privacy') : null;

		function openWhatsApp() {
			trackClick(cfg, true);
			window.open(cfg.whatsappUrl, '_blank', 'noopener,noreferrer');
		}

		function openModal() {
			if (!modal) {
				openWhatsApp();
				return;
			}
			if (titleEl) titleEl.textContent = cfg.gdprTitle || '';
			if (textEl) textEl.textContent = cfg.gdprMessage || '';
			if (confirmBtn) confirmBtn.textContent = cfg.gdprButton || 'Continue';
			if (privacyEl) {
				privacyEl.innerHTML = cfg.privacyUrl
					? '<a href="' + cfg.privacyUrl + '" target="_blank" rel="noopener noreferrer">Privacy Policy</a>'
					: '';
			}
			modal.hidden = false;
		}

		function closeModal() {
			if (modal) {
				modal.hidden = true;
			}
		}

		if (button) {
			button.addEventListener('click', function () {
				if (cfg.gdprEnabled) {
					openModal();
				} else {
					trackClick(cfg, false);
					window.open(cfg.whatsappUrl, '_blank', 'noopener,noreferrer');
				}
			});
		}

		if (confirmBtn) {
			confirmBtn.addEventListener('click', function () {
				closeModal();
				openWhatsApp();
			});
		}

		if (modal) {
			modal.querySelectorAll('[data-rlwc-close]').forEach(function (el) {
				el.addEventListener('click', closeModal);
			});
		}
	}

	function init() {
		var floating = document.getElementById('rlwc-widget-root');
		if (floating) {
			bindInstance(floating, getConfig('floating') || getConfig());
		}

		document.querySelectorAll('[data-rlwc-instance]').forEach(function (root) {
			var instanceId = root.getAttribute('data-rlwc-instance');
			bindInstance(root, getConfig(instanceId));
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
