/**
 * GLOBAL STATE MANAGEMENT - Enhanced Version 2.0
 */
const AppState = (() => {
  const state = {
    activeRequests: new Map(),
    isInitialized: false,
    networkStatus: navigator.onLine ? 'good' : 'offline',
    timers: {},
    lastRequest: null,
    navigationHistory: []
  };

  const updateNetworkStatus = () => {
    if (navigator.connection) {
      state.networkStatus = navigator.connection.effectiveType.includes('2g') ? 'slow' : 'good';
    }
  };

  const setupNetworkListeners = () => {
    window.addEventListener('offline', () => {
      state.networkStatus = 'offline';
      console.warn('Network status: offline');
    });
    
    window.addEventListener('online', () => {
      state.networkStatus = 'good';
      console.info('Network status: online');
    });
  };

  const addToHistory = (url, target, content) => {
    state.navigationHistory.push({ url, target, content });
    if (state.navigationHistory.length > 10) {
      state.navigationHistory.shift();
    }
  };

  return {
    getState: () => ({...state}),
    init: () => {
      if (state.isInitialized) return;
      state.isInitialized = true;
      updateNetworkStatus();
      setupNetworkListeners();
      console.debug('AppState initialized');
    },
    addRequest: (key, controller) => {
      state.activeRequests.set(key, { controller, timestamp: Date.now() });
      state.lastRequest = { key, controller };
    },
    abortRequest: (key) => {
      if (state.activeRequests.has(key)) {
        state.activeRequests.get(key).controller.abort();
        state.activeRequests.delete(key);
      }
    },
    clearTimers: () => {
      Object.values(state.timers).forEach(clearTimeout);
      state.timers = {};
    },
    getLastRequest: () => state.lastRequest,
    addToHistory,
    getHistory: () => [...state.navigationHistory]
  };
});


/**
 * CORE FUNCTIONALITY - Refactored Version 2.0
 */
const Core_Menu = {
  async loadContent(url, target = '.conteudo', form = null) {
    if (!url || url.startsWith('javascript:')) {
      console.error('[Core_Menu] Invalid URL blocked:', url);
      return false;
    }

    // Normalize URL
    if (!url.startsWith('http') && !url.startsWith('/')) {
      url = `/${url}`;
    }

    const absoluteUrl = url.startsWith('http') ? url : `${window.location.origin}${url}`;
    const urlObj = new URL(absoluteUrl);
    urlObj.searchParams.set('_', Date.now());

    const fetchOptions = {
      method: form ? 'POST' : 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      cache: 'no-store',
      credentials: 'same-origin'
    };

    if (form) {
      fetchOptions.body = new FormData(form);
    }

    const controller = new AbortController();
    fetchOptions.signal = controller.signal;
    AppState.addRequest(urlObj.toString(), controller);

    try {
      this.showLoader(target);
      const response = await fetch(urlObj.toString(), fetchOptions);

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`HTTP ${response.status}: ${errorText || 'No details'}`);
      }

      const html = await response.text();
      const container = this.getContainer(target);
      const previousContent = container.innerHTML;
      
      container.innerHTML = this.sanitizeContent(html);
      AppState.addToHistory(urlObj.toString(), target, previousContent);
      
      await this.processDynamicElements(container);
      this.fixRelativeLinks(container, target);
      
      return true;
    } catch (error) {
      console.error('[Core_Menu] Load error:', { url: urlObj.toString(), error });
      this.displayError(target, error);
      return false;
    } finally {
      this.hideLoader();
      AppState.abortRequest(urlObj.toString());
    }
  },

  showLoader(target) {
    const container = this.getContainer(target);
    const loader = document.createElement('div');
    loader.className = 'content-loader';
    loader.innerHTML = `
      <div class="loader-spinner"></div>
      <div class="loader-text">Carregando...</div>
    `;
    container.appendChild(loader);
  },

  hideLoader() {
    document.querySelectorAll('.content-loader').forEach(el => el.remove());
  },

  getContainer(selector) {
    const containers = ['.conteudo', '.resultadoCadastro', '.retorno', selector];
    
    for (const sel of containers) {
      const container = document.querySelector(sel);
      if (container) return container;
    }

    const fallback = document.createElement('div');
    fallback.className = 'dynamic-content-container';
    document.body.appendChild(fallback);
    return fallback;
  },

  sanitizeContent(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;
    
    // Sanitize select options
    temp.querySelectorAll('select option').forEach(option => {
      option.textContent = option.textContent
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<\/?b>/gi, '');
      
      if (option.innerHTML.includes('<b>')) option.dataset.bold = 'true';
      if (option.innerHTML.includes('<br>')) option.dataset.separator = 'true';
    });

    return temp.innerHTML;
  },

  async processDynamicElements(container) {
    const scripts = Array.from(container.querySelectorAll('script'));
    
    for (const script of scripts) {
      try {
        if (script.src && !script.src.startsWith(window.location.origin)) {
          console.warn('[Core_Menu] Blocked external script:', script.src);
          continue;
        }

        const newScript = document.createElement('script');
        [...script.attributes].forEach(attr => {
          newScript.setAttribute(attr.name, attr.value);
        });

        if (script.src) {
          await new Promise((resolve, reject) => {
            newScript.onload = resolve;
            newScript.onerror = reject;
            document.head.appendChild(newScript);
          });
        } else {
          newScript.textContent = script.textContent;
          document.body.appendChild(newScript);
        }
      } catch (error) {
        console.error('[Core_Menu] Script error:', error);
      }
      script.remove();
    }
  },

  fixRelativeLinks(container, target) {
    container.querySelectorAll('a').forEach(link => {
      const href = link.getAttribute('href');
      if (href && !href.startsWith('http') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          this.loadContent(href, target);
        });
      }
    });
  },

  displayError(target, error) {
    const container = this.getContainer(target);
    container.innerHTML = `
      <div class="content-error">
        <i class="fas fa-exclamation-circle"></i>
        <h4>Erro ao Carregar</h4>
        <p>${error.message.replace('<', '&lt;').replace('>', '&gt;')}</p>
        <div class="error-actions">
          <button class="btn btn-retry" onclick="App.retryLastRequest()">
            <i class="fas fa-redo"></i> Tentar Novamente
          </button>
          <button class="btn btn-reload" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Recarregar Página
          </button>
        </div>
      </div>`;
  },

  post(path, formData = null, method = 'POST') {
    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    if (formData) {
      for (const [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
      }
    }

    document.body.appendChild(form);
    form.submit();
  }
};

/**
 * UI CONTROLLER - Refactored Version 2.0
 */
const UIController = {
  menuConfig: [
    { id: 1, selector: '.list-finac', icon: 'fa-dollar-sign', label: 'Financeiro' },
    { id: 2, selector: '.list-comp', icon: 'fa-hand-holding-usd', label: 'Compras' },
    { id: 3, selector: '.list-consulta', icon: 'fa-search', label: 'Consultas' },
    { id: 4, selector: '.list-gestor', icon: 'fa-user-secret', label: 'Gestor' }
  ],

  init() {
    this.setupMenuToggles();
    this.setupFormHandlers();
    this.setupEventDelegation();
    this.hideAllMenuBlocks();
    console.debug('UIController initialized');
  },

  setupMenuToggles() {
    this.menuConfig.forEach(({id, selector, label}) => {
      const handler = this.debounce(() => {
        const elements = document.querySelectorAll(selector);
        const isVisible = elements[0]?.style.display !== 'none';
        
        elements.forEach(el => {
          el.style.display = isVisible ? 'none' : 'block';
          el.setAttribute('aria-expanded', !isVisible);
        });
        
        console.debug(`Menu ${label} ${isVisible ? 'fechado' : 'aberto'}`);
      }, 150);
      
      window[`toggleMenu${id}`] = handler;
      window[`clickFunction${id}`] = handler;
    });
  },

  setupFormHandlers() {
    document.querySelectorAll('form').forEach(form => {
      form.onsubmit = null;
      
      if (!form.hasAttribute('data-ajax') && !form.hasAttribute('data-no-ajax')) {
        form.setAttribute('data-no-ajax', 'true');
      }
      
      if (form.action?.includes('javascript:')) {
        console.warn('Form com action inválida:', form);
        form.action = '#';
      }
    });
  },

  setupEventDelegation() {
    document.body.addEventListener('click', (e) => {
      const ajaxLink = e.target.closest('[data-ajax]');
      if (ajaxLink) this.handleAjaxClick(e, ajaxLink);

      const actionBtn = e.target.closest('[data-action]');
      if (actionBtn) this.handleActionButton(e, actionBtn);
    });

    document.addEventListener('submit', this.handleFormSubmit.bind(this));
  },

  handleAjaxClick(e, element) {
    e.preventDefault();
    const target = element.dataset.target || '.conteudo';
    element.classList.add('loading');
    
    Core.loadContent(element.href, target)
      .finally(() => {
        element.classList.remove('loading');
      });
  },

  handleFormSubmit(e) {
    const form = e.target;
    
    if (form.hasAttribute('data-no-ajax')) {
      if (!form.action || form.action === 'javascript:void(0)') {
        e.preventDefault();
        console.error('Form submission blocked - invalid action:', form);
      }
      return;
    }

    e.preventDefault();
    const submitBtn = form.querySelector('[type="submit"]');
    
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.classList.add('loading');
    }

    const target = form.dataset.target || '.resultadoCadastro, .retorno';
    
    Core.loadContent(form.action, target, form)
      .finally(() => {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.classList.remove('loading');
        }
      });
  },

  handleActionButton(e, button) {
    e.preventDefault();
    const form = button.closest('form');
    
    button.disabled = true;
    button.classList.add('loading');

    const target = button.dataset.target || form?.dataset.target || '.conteudo';
    const action = button.dataset.action || form?.action;

    if (action && action !== 'javascript:void(0)') {
      Core.loadContent(action, target, form)
        .finally(() => {
          button.disabled = false;
          button.classList.remove('loading');
        });
    } else {
      button.disabled = false;
      button.classList.remove('loading');
      console.error('Botão com ação inválida:', button);
    }
  },

  debounce(func, wait, immediate = false) {
    let timeout;
    return function() {
      const context = this;
      const args = arguments;
      
      const later = () => {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      
      const callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      
      if (callNow) func.apply(context, args);
    };
  },

  hideAllMenuBlocks() {
    document.querySelectorAll('.list-block').forEach(el => {
      el.style.display = 'none';
      el.setAttribute('aria-expanded', 'false');
    });
  }
};

/**
 * GLOBAL EXPORTS AND INITIALIZATION
 */
window.App = {
  init() {
    this.fixAutoFocusIssues();
    AppState.init();
    UIController.init();
    this.setupGlobalErrorHandling();
    this.setupNavigationHandlers();
    console.info('Aplicação inicializada');
  },

  fixAutoFocusIssues() {
    document.querySelectorAll('[autofocus]:not(:first-of-type)').forEach(el => {
      el.removeAttribute('autofocus');
    });
  },

  setupGlobalErrorHandling() {
    window.addEventListener('error', (e) => {
      console.error('[Global Error]', e.message, e.filename, e.lineno);
    });

    window.addEventListener('unhandledrejection', (e) => {
      console.error('[Unhandled Promise]', e.reason);
    });
  },

  setupNavigationHandlers() {
    window.addEventListener('popstate', (event) => {
      if (event.state?.previousContent) {
        const container = document.querySelector(event.state.target || '.conteudo');
        if (container) {
          container.innerHTML = event.state.previousContent;
          Core.fixRelativeLinks(container, event.state.target);
        }
      }
    });
  },

  retryLastRequest() {
    const lastRequest = AppState.getLastRequest();
    if (lastRequest?.key) {
      const target = lastRequest.key.includes('resultado') ? '.resultadoCadastro' : '.conteudo';
      Core.loadContent(lastRequest.key, target);
    }
  },

  getNetworkStatus() {
    return AppState.getState().networkStatus;
  }
};

/**
 * GLOBAL FUNCTIONS
 */
window.ldy = function(url, target = '.conteudo') {
  if (!url.startsWith('http') && !url.startsWith('/') && !url.startsWith('../')) {
    url = '/restrito/' + url;
  }
  return Core.loadContent(url, target);
};

window.post = Core.post.bind(Core_Menu);

// Initialize application
document.addEventListener('DOMContentLoaded', () => {
  App.init();
});

// Fallback initialization
if (document.readyState !== 'loading') {
  App.init();
}