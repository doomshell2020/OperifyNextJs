const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('designsheet', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    designsheetno: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contract_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    design_sheet: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    item_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    quantity: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    r1: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    r2: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    r3: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    r5: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    r4: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    datefrom: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'designsheet',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
