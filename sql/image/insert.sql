INSERT IGNORE INTO IB01_IMAGE_STORE
(
	HASH,
	BODY
)
VALUES
(
	:hash,
	:body
);
