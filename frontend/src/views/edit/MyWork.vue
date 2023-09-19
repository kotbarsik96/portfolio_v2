<template>
    <section id="add-work">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            работы
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" label="Название" placeholder="Название" id="work-title"
                    name="work_title" :value="workData.title"></InputItem>
            </div>
            <div class="add__inputs-section">
                <div class="input-item">
                    <span class="input-item__label">
                        Метка
                    </span>
                    <MySelect :values="[
                        'Лендинг',
                        'Интернет-магазин'
                    ]"></MySelect>
                </div>
            </div>
            <div class="add__inputs-section">
                <MyFilter class="modal" allowComment></MyFilter>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Десктоп версия"></LoadImage>
                </div>
                <div class="add__load-media-item add__load-media-item--mobile">
                    <LoadImage title="Мобильная версия"></LoadImage>
                </div>
            </div>
            <div class="add__buttons">
                <button v-if="$route.meta.isEditing" class="button button--color_2" type="button" @click="update">
                    Сохранить
                </button>
                <button v-if="$route.meta.isEditing" class="button" type="button" @click="remove">
                    Удалить
                </button>
                <button v-else class="button button--color_2" type="button" @click="load">
                    Добавить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import MyFilter from '@/components/global/MyFilter.vue';
import LoadImage from '@/components/private/LoadImage.vue';

export default {
    name: 'MyWork',
    components: {
        MyFilter,
        LoadImage
    },
    async mounted() {
    },
    data() {
        return {
            workData: {}
        }
    },
    methods: {
    },
}
</script>

<style></style>