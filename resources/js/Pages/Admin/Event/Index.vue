<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedAdmin.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  events: {
    type: Object,
    default: () => ({}),
  },
  can: {
    type: Object,
    default: () => ({}),
  },
});
const form = useForm();
function destroy(id) {
    if (confirm("Jeste li sigurni da želite obrisati događaj?")) {
        form.delete(route('event.destroy', id));
    }
}

function isDateGreaterThanToday(date) {
      const today = new Date();
      const myDate = new Date(date);
      today.setHours(0, 0, 0, 0); // Set the time to midnight for accurate comparison

      return myDate.getTime() > today.getTime();
    }


</script>


<template>
    <Head title="Događaji" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Događaji
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-5">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex bg-white border-b border-gray-200 justify-between items=center p-5">
                        <div class="flex space-x-2 items-center">
                            Svi događaji
                        </div>
                        <div class="flex space-x-2 items-center">
                            <a
                                className="px-6 py-2 text-white bg-green-500 rounded-md focus:outline-none"
                                :href="route('event.create')"
                            >
                                Kreiraj događaj
                        </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-blue-200">
                            <tr>
                                <th scope="col" class="py-3 px-6">Naziv</th>
                                <th scope="col" class="py-3 px-6">Opis</th>
                                <th scope="col" class="py-3 px-6">Slika</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Opcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="n in events.data" :key="n.id" class="bg-white border-b border-blue-200">
                                <td data-label="Name" class="py-4 px-6">
                                    {{ n.name }}
                                </td>
                                <td data-label="Description" class="py-4 px-6">
                                    {{ n.description.substring(0,100)+"..." }}

                                </td>
                                <td data-label="Image" class="py-4 px-6">
                                    <img :src="n.image" width="50" />
                                </td>
                                <td class="text-center">
                                    <span v-if="isDateGreaterThanToday(n.date)"
                                        class="inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pb-2 pt-2 text-center align-baseline text-[0.75em] font-bold leading-none text-green-700">
                                        U toku 
                                        </span>  
                                        <span v-else
                                        class="inline-block whitespace-nowrap rounded-full bg-red-100 px-[0.65em] pb-2 pt-2 text-center align-baseline text-[0.75em] font-bold leading-none text-red-700">
                                        Istekao
                                        </span>
                                </td>
                                <td
                                   
                                    class="py-4 px-6 w-96"
                                >
                                    <div type="justify-start lg:justify-end" no-wrap>
                                        <a
                                            tabIndex="1"
                                            className="px-4 py-2 mr-1 text-sm text-white bg-green-500 rounded"
                                            :href="route('event.show', n.id)"
                                        >
                                            Više
                                        </a>
                                        <a
                                            tabIndex="1"
                                            className="px-4 py-2 text-sm text-white bg-blue-500 rounded"
                                            :href="route('event.edit', n.id)"
                                        >
                                            Uredi
                                        </a>
                                        <button
                                            @click="destroy(n.id)"
                                            tabIndex="-1"
                                            type="button"
                                            className="mx-1 px-4 py-2 text-sm text-white bg-red-500 rounded"
                                        >
                                            Obriši
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination class="mt-6" :links="events.links" />
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>