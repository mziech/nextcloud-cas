const path = require('path');
const webpack = require('webpack');
const webpackConfig = require('@nextcloud/webpack-vue-config');
const {WebpackManifestPlugin} = require("webpack-manifest-plugin");

delete webpackConfig.entry['main'];
webpackConfig.entry['settings'] = path.join(__dirname, 'src', 'settings.js');
webpackConfig.entry['login'] = path.join(__dirname, 'src', 'login.js');
webpackConfig.plugins.push(new WebpackManifestPlugin());
module.exports = webpackConfig;
