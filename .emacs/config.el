(defvar current-project (project-get-root)
  "Current project directory")

;; run project
(defrunc project
  (let ((launcher (project-config-file-directory-get-path "launcher")))
	(vterm-run (format "sh %s" launcher) "*telegram-bot*")))

(global-set-key (kbd "S-<f10>") 'run-project)
(define-key php-mode-map (kbd "S-<f10>") 'run-project)

;; open terminal and launch container
(defrunc container
  (vterm-run "clear; docker-symfony" "*symfony-container*"))

(global-set-key (kbd "C-c C-`") 'run-container)

;; add snippets
(let ((snippet-directory (project-config-file-directory-get-path "snippets")))
  (add-to-list 'yas-snippet-dirs 'snippet-directory)
  (yas-load-directory snippet-directory))
