<template>
    <div class="filter" :class="{ '__shown': isBodyShown }">
        <button v-if="isHideable" class="filter__tab-button icon-filter" type="button" @click="toggleBody">
            <h4 class="filter__title">
                {{ title }}
            </h4>
        </button>
        <div class="filter__body" ref="filterBody">
            <div v-for="section in body" :key="section.title" :data-section-name="section.name" class="filter__section">
                <h5 class="filter__section-title">
                    {{ section.title }}
                </h5>
                <ul class="filter__section-items">
                    <li v-for="(value, index) in section.values" :key="value" class="filter__section-item">
                        <label v-if="getInputType(section) === 'checkbox'" class="filter__section-label checkbox-label">
                            <input type="checkbox" ref="filterInputButton" :name="section.name" :value="value"
                                v-model="checkedValues">
                            <span class="checkbox-label__box"></span>
                            <span class="checkbox-label__text">
                                {{ value }}
                            </span>
                        </label>
                        <label v-if="getInputType(section) === 'radio'" class="filter__section-label radio-label">
                            <input type="radio" ref="filterInputButton" :name="section.name" :value="value"
                                v-model="radioModels[section.name]">
                            <span class="radio-label__circle"></span>
                            <span class="radio-label__text">
                                {{ value }}
                            </span>
                        </label>
                        <div v-if="isAnyAttachmentAllowed(section)" class="filter__section-item-buttons">
                            <button v-if="section.allowComment" class="filter__section-item-write-button icon-pencil"
                                type="button" @click="writeComment"></button>
                            <button v-if="section.allowUrl" class="filter__section-item-url-button icon-attachment"
                                @click="attachUrl"></button>
                        </div>
                        <div v-if="isAnyAttachmentAllowed(section)" class="filter__section-item-attachments">
                            <span v-if="section.allowComment"
                                class="filter__section-item-attachment filter__section-item-comment editable" ref="comment"
                                @blur="onCommentBlur" @input="updateAttachments"></span>
                            <a v-if="section.allowUrl" class="filter__section-item-attachment filter__section-item-url"
                                ref="attachedUrl" href="#"></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { getHeight, onTransitionEnd, setEditable, delay, qsAll } from '@/assets/scripts/scripts.js';
import { nextTick, h } from 'vue';
import { useModalsStore } from '@/stores/modals.js';
import TextInput from './TextInput.vue';

export default {
    name: 'MyFilter',
    emits: ['update:modelValue'],
    props: {
        modelValue: Array,
        body: {
            /* body: [
                { 
                    title: 'Тип', 
                    isRadio: true, 
                    values: ['Вёрстка по макету', 'Интеграция с backend'], 
                    allowComment: true 
                }
            ]*/
            type: Array,
            required: true
        },
        isHideable: Boolean,
        title: {
            type: String,
            default: 'Фильтр'
        }
    },
    mounted() {
        const self = this;
        const media = window.matchMedia('(max-width: 1279px)');
        media.addEventListener("change", onMediaChange);
        onMediaChange();
        setFirstRadioChecked();

        function onMediaChange() {
            if (media.matches) {
                self.toggleBody(false);
            } else {
                self.toggleBody(true);
            }
        }
        function setFirstRadioChecked() {
            const radio = self.body.filter(obj => obj.isRadio);
            radio.forEach(async obj => {
                const value = obj.values[0];
                const input = self.$refs.filterInputButton.find(inp => inp.value === value);
                if (!input)
                    return;

                await delay(0);
                input.checked = true;
                input.dispatchEvent(new Event('change'));
            });
        }
    },
    data() {
        return {
            isBodyShown: false,
            isTogglingBody: false,
            insertedUrl: '',
            checkedValues: [],
            radioModels: {}
        }
    },
    computed: {
        checkedArray() {
            function handleInput(input) {
                if (!input)
                    return;

                const sectionNameDatasetNode = input.closest('[data-section-name]');
                if (!sectionNameDatasetNode)
                    return;

                const value = input.value;
                const name = sectionNameDatasetNode.dataset.sectionName.trim();
                const li = self.closestSectionItem(input);
                const commentSpan = li.querySelector('.filter__section-item-comment');
                const urlNode = li.querySelector('.filter__section-item-url');

                const description = commentSpan ? commentSpan.textContent : null;
                const url = urlNode ? urlNode.textContent : null;

                const obj = { value, input, description, url };
                const inArr = array.find(o => o.name === name);
                if (inArr)
                    inArr.values.push(obj);
                else {
                    array.push({ name, values: [obj] });
                }
            }

            const self = this;
            let array = [];
            for (let value of this.checkedValues) {
                const input = this.$refs.filterInputButton.find(inp => inp.value.trim() === value.trim());
                handleInput(input);
            }
            for (let key in this.radioModels) {
                const value = this.radioModels[key];
                const input = this.$refs.filterInputButton.find(inp => inp.value.trim() === value.trim());
                handleInput(input);
            }
            return array;
        }
    },
    methods: {
        getInputType(section) {
            if (section.isRadio)
                return 'radio';

            return 'checkbox';
        },
        isAnyAttachmentAllowed(section) {
            return section.allowComment
                || section.allowUrl;
        },
        closestSectionItem(relative) {
            return relative.closest('.filter__section-item');
        },
        async toggleBody(bool = null) {
            if (typeof bool !== 'boolean')
                this.isBodyShown = !this.isBodyShown;
            else this.isBodyShown = bool;

            if (this.isBodyShown)
                this.showBody();
            else
                this.hideBody();
        },
        async onBodyTransEnd() {
            if (!this.$refs.filterBody)
                return;

            await onTransitionEnd(this.$refs.filterBody);
            if (!this.$refs.filterBody)
                return;
            
            this.$refs.filterBody.style.removeProperty('transition');
            this.isTogglingBody = false;
        },
        async showBody(isForced = false) {
            if (this.isTogglingBody && !isForced)
                return;

            this.isTogglingBody = true;
            this.isBodyShown = true;

            const fbody = this.$refs.filterBody;
            if (!fbody)
                return;

            const height = getHeight(fbody);
            fbody.style.removeProperty('padding');
            fbody.style.removeProperty('margin');
            fbody.style.cssText = `
                transition: all .3s;
                max-height: ${height + 100}px;
            `;
            await onTransitionEnd(fbody);

            this.onBodyTransEnd();
        },
        hideBody() {
            if (this.isTogglingBody)
                return;

            if (!this.isHideable) {
                this.showBody();
                return;
            }

            this.isTogglingBody = true;
            this.isBodyShown = false;

            const fbody = this.$refs.filterBody;
            if (!fbody)
                return;

            fbody.style.cssText = `
                transition: all .3s;
                padding: 0;
                margin: 0;
                max-height: 0px;
            `;

            this.onBodyTransEnd();
        },
        writeComment(event) {
            const comment = this.$refs.comment.find(node => node.closest('li') === event.target.closest('li'));
            setEditable(comment);
        },
        onCommentBlur(event) {
            event.target.removeAttribute('contenteditable');
        },
        refresh() {
            this.checkedValues = [];
            this.$refs.filterInputButton.forEach(inp => inp.checked = false);
            this.$refs.comment.forEach(span => span.textContent = '');
            this.$refs.attachedUrl.forEach(link => {
                link.textContent = '';
                link.setAttribute('href', '#')
            });
            this.updateAttachments();
        },
        async setChecked(title, attachments = {}) {
            await delay(0);

            const input = this.$refs.filterInputButton.find(inp => inp.value.trim() === title.trim());
            if (!input)
                return;

            input.checked = true;
            const li = this.closestSectionItem(input);
            if (attachments) {
                const commentNode = li.querySelector('.filter__section-item-comment');
                const urlNode = li.querySelector('.filter__section-item-url');

                if (commentNode && attachments.comment) {
                    commentNode.textContent = attachments.comment;
                }
                if (urlNode && attachments.url) {
                    urlNode.textContent = attachments.url;
                    urlNode.setAttribute('href', attachments.url);
                }

                this.updateAttachments();
            }
            input.dispatchEvent(new Event('change'));
        },
        updateAttachments() {
            this.toggleBody(true);

            // обновит checkedArray
            this.checkedValues = this.checkedValues.map(v => v);
        },
        attachUrl(event) {
            const self = this;

            const urlNode = this.closestSectionItem(event.target)
                .querySelector('.filter__section-item-url');
            if (!urlNode)
                return;
            const currentUrl = urlNode.textContent.trim();
            if (!urlNode)
                return;

            const modalsStore = useModalsStore();
            const textInputComp = h(TextInput, { name: 'test', inputId: 'test', placeholder: 'url', defaultValue: currentUrl });
            modalsStore.addModalWindow({
                title: 'Прикрепить URL к навыку',
                body: [textInputComp],
                modalClassNames: 'modal-window--with-input',
                confirm: {
                    title: 'Прикрепить',
                    callback(modalWindowCtx) {
                        const url = modalWindowCtx.textInputValue;
                        urlNode.textContent = url;
                        urlNode.setAttribute('href', url);
                        self.updateAttachments();
                    }
                },
                decline: {
                    title: 'Очистить',
                    callback() {
                        urlNode.textContent = '';
                        urlNode.setAttribute('href', '');
                        self.updateAttachments();
                    }
                }
            });
        },
    },
    watch: {
        async body() {
            if (this.isBodyShown) {
                await nextTick();
                this.showBody(true);
            }
        },
        checkedArray: {
            deep: true,
            handler() {
                this.$emit('update:modelValue', this.checkedArray);
            }
        }
    }
}
</script>