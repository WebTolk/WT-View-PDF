const entry = {
	"bootstrap.modal": {
		import: './plg_content_wtviewpdf/es6/bootstrap.modal.es6',
		filename: 'bootstrap.modal.js',
	},
	"default": {
		import: './plg_content_wtviewpdf/es6/default.es6',
		filename: 'default.js',
	},
	"uikit.modal": {
		import: './plg_content_wtviewpdf/es6/uikit.modal.es6',
		filename: 'uikit.modal.js',
	}
};

const webpackConfig = require('./webpack.config.js');
const publicPath = '../media';
const production = webpackConfig(entry, publicPath);
const development = webpackConfig(entry, publicPath, 'development');

module.exports = [production, development]