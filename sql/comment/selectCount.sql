SELECT		COUNT(ENTITY_ID)	AS COUNT
	FROM	IB01_INDEX_COMMENT
	WHERE	OWNER = :owner;
