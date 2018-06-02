(function (global, document) {
    const IFRAME_URL = 'https://www.draw.io/?embed=1&proto=json&ui=atlas&spin=1&modified=unsavedChanges';
    const IFRAME_CLASS = 'ez-field-edit--ezdrawio-editor';
    const TARGET_ORIGIN_DRAWIO = 'https://www.draw.io';
    const SELECTOR_DIAGRAM_FIELD = '.ez-field-edit--ezdrawio';
    const SELECTOR_DATA = '.ez-field-edit__data';
    const SELECTOR_DATA_VALUE = '.ez-data-source__input-data';
    const SELECTOR_DATA_ALT_TEXT = 'input[type=text]';
    const SELECTOR_PREVIEW = '.ez-field-edit__preview';
    const SELECTOR_PREVIEW_IMG = 'img';
    const SELECTOR_BTN_CREATE = '.ez-data-source__btn-add';
    const SELECTOR_BTN_EDIT = '.ez-field-edit-preview__action--edit';
    const SELECTOR_BTN_REMOVE = '.ez-field-edit-preview__action--remove';

    class EzDrawIOEditor {
        constructor(container) {
            this.container = container;
            this.editor = null;
        }

        open(diagram, onDiagramChanged) {
            const receive = (event) => {
                if (event.origin === TARGET_ORIGIN_DRAWIO && event.data.length) {
                    const message = JSON.parse(event.data);

                    switch (message.event) {
                        case 'init':
                            this._init(diagram);
                            break;
                        case 'save':
                            this._save();
                            break;
                        case 'export':
                            onDiagramChanged(message.data, message.bounds.width, message.bounds.height);
                        case 'exit':
                            global.removeEventListener('message', receive);
                            this.container.removeChild(this.editor);
                            this.editor = null;
                    }
                }
            };

            this._createEditor();
            global.addEventListener('message', receive);
        }

        _init(data) {
            if (data) {
                this._execAction({action: 'load', xml: data});
            } else {
                this._execAction({action: 'template'});
            }
        }

        _save() {
            this._execAction({
                action: 'export',
                format: 'xmlpng',
                spinKey: 'Saving...'
            });
        }

        _execAction(data) {
            this.editor.contentWindow.postMessage(JSON.stringify(data), TARGET_ORIGIN_DRAWIO);
        }

        _createEditor() {
            this.editor = document.createElement('iframe');
            this.editor.classList.add(IFRAME_CLASS);
            this.editor.setAttribute('frameborder', '0');
            this.editor.setAttribute('width', document.body.clientWidth);
            this.editor.setAttribute('height', document.body.clientHeight);
            this.editor.setAttribute('src', IFRAME_URL);

            this.container.appendChild(this.editor);
        }
    }

    global.addEventListener('load', () => {
        class EzDrawIOValidator extends global.eZ.BaseFieldValidator {
            /**
             * Validates the input
             *
             * @method validateInput
             * @param {Event} event
             * @returns {Object}
             * @memberof EzDrawIOValidator
             */
            validateInput(event) {
                const isRequired = event.target.required;
                const isEmpty = !event.target.value;
                const isError = isRequired && isEmpty;
                const fieldName = event.target.closest(SELECTOR_DIAGRAM_FIELD).querySelector('.ez-field-edit__label').innerHTML;
                const result = {isError};
                if (isEmpty) {
                    result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldName);
                }

                return result;
            }
        }

        document.querySelectorAll(SELECTOR_DIAGRAM_FIELD).forEach((field) => {
            const dataInput = field.querySelector(SELECTOR_DATA_VALUE);
            const altInput = field.querySelector(SELECTOR_DATA_ALT_TEXT);
            const previewImg = field.querySelector(SELECTOR_PREVIEW_IMG);
            const preview = field.querySelector(SELECTOR_PREVIEW);
            const create = field.querySelector(SELECTOR_DATA);

            const updateUI = (data, width, height) => {
                dataInput.setAttribute('value', data ? data : '');
                previewImg.setAttribute('src', data ? data : '://0');
                (data ? preview : create).removeAttribute('hidden');
                (data ? create : preview).setAttribute('hidden', true);
            };

            const editDiagram = (event) => {
                event.preventDefault();
                (new EzDrawIOEditor(document.body)).open(dataInput.value, updateUI);
            };

            field.querySelector(SELECTOR_BTN_CREATE).addEventListener('click', editDiagram);
            field.querySelector(SELECTOR_BTN_EDIT).addEventListener('click', editDiagram);
            field.querySelector(SELECTOR_BTN_REMOVE).addEventListener('click', (event) => {
                event.preventDefault();
                updateUI();
                altInput.value = '';
            });
        });

        const validator = new EzDrawIOValidator({
            classInvalid: 'is-invalid',
            fieldSelector: SELECTOR_DIAGRAM_FIELD,
            eventsMap: [
                {
                    selector: `${SELECTOR_DIAGRAM_FIELD} ${SELECTOR_DATA_VALUE}`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: [
                        `${SELECTOR_DIAGRAM_FIELD} .ez-field-edit__label-wrapper`
                    ],
                }
            ]
        });

        validator.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });
})(window, document);
