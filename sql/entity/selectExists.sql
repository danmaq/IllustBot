SELECT		COUNT(ID) > 0 AS EXIST
	FROM	IB01_ENTITIES
	WHERE	ID = :id;
