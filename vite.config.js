import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
	plugins: [vue()],
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