SELECT		BODY,
			UNIX_TIMESTAMP(UPDATED)	AS UPDATED
	FROM	IB01_ENTITIES
	WHERE	ID = :id;
