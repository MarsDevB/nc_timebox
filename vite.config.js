import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import { copyFileSync } from 'fs'

// Copies the built CSS to css/ where templates/main.php loads it from
function copyCssPlugin() {
	return {
		name: 'copy-css-to-css-dir',
		closeBundle() {
			copyFileSync(
				resolve(__dirname, 'js/timebox-main.css'),
				resolve(__dirname, 'css/timebox-main.css'),
			)
		},
	}
}

export default defineConfig({
	plugins: [vue(), copyCssPlugin()],
	build: {
		outDir: resolve(__dirname, 'js'),
		emptyOutDir: false,
		rollupOptions: {
			input: {
				'timebox-main': resolve(__dirname, 'src/main.js'),
			},
			output: {
				entryFileNames: '[name].js',
				chunkFileNames: '[name].js',
				assetFileNames: '[name].[ext]',
			},
		},
		sourcemap: true,
	},
	resolve: {
		alias: {
			'@': resolve(__dirname, 'src'),
		},
	},
	define: {
		'process.env': {},
	},
})