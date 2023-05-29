<!--
 * @copyright Copyright (c) 2021 Marco Ziech <marco+nc@ziech.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 -->

<template>
	<div id="cas" class="section">
		<h2>{{ t("cas", "CAS Server") }}</h2>

		<p v-if="baseUrl" class="settings-hint">
			{{ t("cas", "Client should be configured to use the following CAS base URL: ") }}<br>
			<a :href="baseUrl">{{ baseUrl }}</a>
		</p>

		<h3>{{ t("cas", "Service Providers") }}</h3>

		<div v-if="services !== undefined" id="cas-clients">
			<table class="grid">
				<thead>
					<tr>
						<th>{{ t("cas", "ID") }}</th>
						<th>{{ t("cas", "URL") }}</th>
						<th>{{ t("cas", "URL match type") }}</th>
						<th>{{ t("cas", "Groups") }}</th>
						<th>
							<abbr :title="l10n.strictTooltip">{{ t("cas", "Strict") }}</abbr>
						</th>
						<th />
					</tr>
				</thead>
				<tbody id="cas-services">
					<tr v-for="(service, index) in services" :key="index">
						<td><input v-model="service.id" type="text"></td>
						<td><input v-model="service.url" type="text"></td>
						<td>
							<select v-model="service.urlMatchType">
								<option value="PREFIX">{{ t("cas", "Match prefix") }}</option>
								<option value="EXACT">{{ t("cas", "Exact match") }}</option>
								<option value="REGEX">{{ t("cas", "Regular Expression") }}</option>
							</select>
						</td>
						<td>
							<NcMultiselect v-model="service.groups"
								:options="groups"
								class="multiselect-vue multiselect--multiple"
								label="label"
								track-by="id"
								:close-on-select="false"
								:multiple="true"
								:placeholder="l10n.unrestricted" />
						</td>
						<td><input v-model="service.strict" type="checkbox"></td>
						<td>
							<button class="icon-delete js-cas-delete" @click="removeService(index)">
								&nbsp;
							</button>
						</td>
					</tr>
				</tbody>
			</table>

			<button id="cas-add-service" class="button" @click="addService()">
				{{ t("cas", "Add service provider") }}
			</button>
			<button id="cas-save" class="button primary" @click="save()">
				{{ t("cas", "Save") }}
			</button>
			<span v-if="saving" id="cas-saving">
				<span class="icon-loading-small inlineblock" />
				{{ t("cas", "Saving changes ...") }}
			</span>
		</div>
		<div v-if="services === undefined" id="cas-clients-loading">
			<span class="icon-loading-small inlineblock" />
			{{ t("cas", "Loading list of CAS clients ...") }}
		</div>
	</div>
</template>

<script lang="js">
import { showError } from '@nextcloud/dialogs'
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import {NcMultiselect} from "@nextcloud/vue";
import axios from '@nextcloud/axios'
import Vue from 'vue'

export default Vue.extend({
	name: 'App',
	components: {
		NcMultiselect,
	},
	computed: {
		l10n: () => ({
			// l10n.pl is unable to detect translations in :-style attributes:
			unrestricted: t('cas', 'Unrestricted'),
			strictTooltip: t('cas', 'Strictly adhere to CAS specification, do not send attributes for 2.0 tickets.'),
		}),
	},
	data() {
		return {
			baseUrl: undefined,
			services: undefined,
			groups: undefined,
			saving: false,
		}
	},
	mounted() {
		axios.get(generateUrl('/apps/cas/admin')).then(response => {
			if (response.status < 300) {
				this.baseUrl = response.data.baseUrl
				this.groups = Object.entries(response.data.groups).map(entry =>
					({ id: entry[0], label: entry[1] }))
				this.services = response.data.services.map(service => ({
					...service,
					groups: service.groups.map(id =>
						({ id, label: response.data.groups[id] })),
				}))
			}
		})
	},
	methods: {
		t,
		addService() {
			if (typeof this.services !== 'undefined') {
				this.services.push({
					id: '',
					url: '',
					urlMatchType: 'PREFIX',
					groups: [],
					strict: false,
				})
			}
		},
		removeService(index) {
			if (typeof this.services !== 'undefined') {
				this.services.splice(index, 1)
			}
		},
		save() {
			const services = this.services.map(service => ({
				...service,
				groups: service.groups.map(group => group.id),
			}))
			this.saving = true
			axios.post(generateUrl('/apps/cas/admin'), { services })
				.catch(() => showError(t('cas', 'Failed to save CAS settings')))
				.then(() => { this.saving = false })
		},
	},
})
</script>
