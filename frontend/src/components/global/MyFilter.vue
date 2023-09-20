<template>
    <div class="filter" :class="{ '__shown': isBodyShown }">
        <button v-if="isHideable" class="filter__tab-button icon-filter" type="button" @click="toggleBody">
            <h4 class="filter__title">
                {{ title }}
            </h4>
        </button>
        <div class="filter__body" ref="filterBody">
            <div v-for="section in body" :key="section.title" class="filter__section">
                <h5 class="filter__section-title">
                    {{ section.title }}
                </h5>
                <ul class="filter__section-items">
                    <li v-for="value in section.values" :key="value" class="filter__section-item">
                        <label class="filter__section-label checkbox-label">
                            <input type="checkbox" ref="filterCheckbox" :name="section.name" :value="value"
                                @change="addToChecked($event, section, value)">
                            <span class="checkbox-label__box"></span>
                            <span class="checkbox-label__text">
                                {{ value }}
                            </span>
                        </label>
                        <span v-if="section.allowComment" class="filter__section-item-comment editable" ref="comment"
                            @blur="onCommentBlur" @input="onCommentInput($event, section)"></span>
                        <button v-if="section.allowComment" class="filter__section-item-write-button icon-pencil"
                            type="button" @click="writeComment"></button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { getHeight, onTransitionEnd, setEditable, getTextContent } from '@/assets/scripts/scripts.js';
import { nextTick } from 'vue';

export default {
    name: 'MyFilter',
    emits: ['update:modelValue'],
    props: {
        modelValue: Array,
        body: {
            /* body: [
                { title: 'Тип', values: ['Вёрстка по макету', 'Интеграция с backend'], allowComment: true }
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

        function onMediaChange() {
            if (media.matches) {
                self.toggleBody(false);
            } else {
                self.toggleBody(true);
            }
        }
    },
    data() {
        return {
            isBodyShown: false,
            isTogglingBody: false,
            checkedValues: []
        }
    },
    methods: {
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
            if(!this.$refs.filterBody)
                return;

            await onTransitionEnd(this.$refs.filterBody);
            this.$refs.filterBody.style.removeProperty('transition');
            this.isTogglingBody = false;
        },
        async showBody(isForced = false) {
            if (this.isTogglingBody && !isForced)
                return;

            this.isTogglingBody = true;
            this.isBodyShown = true;

            const fbody = this.$refs.filterBody;
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
        onCommentInput(event, section) {
            this.toggleBody(true);

            const checkbox = this.$refs.filterCheckbox
                .find(node => this.closestSectionItem(node) === this.closestSectionItem(event.target));
            if (checkbox && checkbox.checked) {
                let obj = null;

                for (let o of this.checkedValues) {
                    obj = o.values.find(subO => subO.value === checkbox.value);
                    if (obj)
                        break;
                }
                const value = getTextContent(event.target);
                if (obj)
                    obj.description = value;
            }
        },
        refresh() {
            this.checkedValues.forEach(section => {
                section.values.forEach(obj => {
                    obj.input.checked = false;
                    obj.input.dispatchEvent(new Event('checked'));
                });
            });
        },
        setChecked(value, comment = null) {
            const checkbox = this.$refs.filterCheckbox.find(obj => obj.value === value);
            if (!checkbox)
                return;

            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change'));
            if (comment) {
                const commentSpan = this.$refs.comment
                    .find(node => this.closestSectionItem(node) === this.closestSectionItem(checkbox));
                if (commentSpan) {
                    commentSpan.textContent = comment;
                    commentSpan.dispatchEvent(new Event('input'));
                }
            }
        },
        addToChecked(event, section, value) {
            let obj = this.checkedValues.find(o => o.title === section.title);
            if (!obj) {
                obj = {
                    title: section.title,
                    name: section.name,
                    values: []
                };
                this.checkedValues.push(obj);
            }

            if (event.target.checked) {
                const data = { value, input: event.target };
                if (section.allowComment) {
                    const span = this.$refs.comment
                        .find(node => {
                            return this.closestSectionItem(node) === this.closestSectionItem(event.target);
                        });
                    if (span)
                        data.description = getTextContent(span);
                }

                obj.values.push(data);
            } else {
                const index = obj.values.findIndex(o => o.value === value);
                if (index >= 0)
                    obj.values.splice(index, 1);
            }
        }
    },
    watch: {
        async body() {
            if (this.isBodyShown) {
                await nextTick();
                this.showBody(true);
            }
        },
        checkedValues: {
            deep: true,
            handler() {
                this.$emit('update:modelValue', this.checkedValues);
            }
        }
    }
}
</script>