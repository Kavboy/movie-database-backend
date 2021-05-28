-- count inserts and updates per month
CREATE OR ALTER TRIGGER trigger_after_action_media
    ON [dbo].[medias]
    FOR INSERT, DELETE
    AS
    BEGIN
        DECLARE @month int = MONTH(getdate());
        DECLARE @year bigint = YEAR(getdate());
        DECLARE @action varchar(7);
        SET @action = (CASE
                           WHEN EXISTS(SELECT * FROM inserted)
                               THEN 'Insert' -- Set Action to Insert.
                           WHEN EXISTS(SELECT * FROM deleted)
                               THEN 'Delete' -- Set Action to Deleted.
                        END)
        IF EXISTS (SELECT * FROM [dbo].[statistics] WHERE action = @action AND month = @month AND year = @year)
            BEGIN
                UPDATE [dbo].[statistics]
                    SET
                        count = (SELECT count FROM [dbo].[statistics] WHERE action = @action AND month =  @month AND year = @year) + 1
                    WHERE action = @action AND month =  @month AND year = @year;
            END
        ELSE
            BEGIN
                INSERT INTO [dbo].[statistics] (month, year, action, count) VALUES ( @month, @year, @action, 1);
            END
    END;
